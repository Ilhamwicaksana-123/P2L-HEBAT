<?php

namespace App\Http\Controllers;

use App\Data\CheckoutItem;
use App\Models\Alamat;
use App\Models\KeranjangItem;
use App\Models\PaymentMethod;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $user = $this->authUser();
        $checkoutSource = $this->resolveCheckoutSource($request = request(), $user);
        $checkoutItems = $checkoutSource['items'];

        if ($checkoutItems->isEmpty()) {
            if ($checkoutSource['source'] === 'direct') {
                return redirect()->route('produk.index')
                    ->with('error', 'Produk yang dipilih tidak ditemukan atau sudah tidak aktif.');
            }

            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang masih kosong. Tambahkan produk terlebih dahulu.');
        }

        foreach ($checkoutItems as $item) {
            if (! $item->produk || ! $item->produk->is_active) {
                return $this->redirectUnavailableCheckout($checkoutSource['source']);
            }
        }

        $subtotal = $checkoutItems->sum(fn (CheckoutItem $item) => $item->subtotal);
        $addresses = $user->alamat()->orderByDesc('is_default')->latest()->get();
        $alamat = $addresses->first();
        PaymentMethod::syncDefaults();

        return view('checkout', [
            'cartItems' => $checkoutItems,
            'subtotal' => $subtotal,
            'addresses' => $addresses,
            'alamat' => $alamat,
            'checkoutSource' => $checkoutSource['source'],
            'directProduct' => $checkoutSource['product'],
            'directQty' => $checkoutSource['qty'],
            'paymentMethods' => PaymentMethod::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function process(Request $request): RedirectResponse
    {
        $activePaymentMethods = PaymentMethod::activeCodes();

        $validated = $request->validate([
            'alamat_mode' => ['required', 'in:existing,new'],
            'alamat_id' => ['nullable', 'integer'],
            'nama_penerima' => ['nullable', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:70'],
            'kota' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'metode_pembayaran' => ['required', 'in:' . implode(',', $activePaymentMethods)],
            'checkout_source' => ['nullable', 'in:cart,direct'],
            'produk_id' => ['nullable', 'integer'],
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $user = $this->authUser();
        $checkoutSource = $this->resolveCheckoutSourceFromValidated($user, $validated);
        $items = $checkoutSource['items'];

        if ($items->isEmpty()) {
            if ($checkoutSource['source'] === 'direct') {
                return redirect()->route('produk.index')
                    ->with('error', 'Produk checkout langsung tidak ditemukan atau sudah tidak aktif.');
            }

            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang masih kosong. Tidak ada pesanan yang bisa diproses.');
        }

        foreach ($items as $item) {
            if (! $item->produk || ! $item->produk->is_active) {
                return $this->redirectUnavailableCheckout($checkoutSource['source']);
            }
        }

        $selectedAddress = $this->resolveAlamatForCheckout($request, $user, $validated);

        $pesanan = DB::transaction(function () use ($user, $validated, $items, $selectedAddress, $checkoutSource) {
            $user->alamat()->update([
                'is_default' => false,
            ]);

            $selectedAddress->forceFill([
                'is_default' => true,
            ])->save();

            $subtotal = $items->sum(fn (CheckoutItem $item) => $item->subtotal);

            $pesanan = Pesanan::create([
                'id_user' => $user->id_user,
                'id_alamat' => $selectedAddress->id_alamat,
                'kode_pesanan' => $this->makeOrderCode(),
                'status_pesanan' => $validated['metode_pembayaran'] === Pesanan::METODE_COD
                    ? Pesanan::STATUS_DIPROSES
                    : Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'total_harga' => $subtotal,
            ]);

            foreach ($items as $item) {
                PesananDetail::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item->produk->id_produk,
                    'nama_produk' => $item->produk->nama_produk,
                    'harga_produk' => $item->harga_satuan,
                    'jumlah_barang' => $item->qty,
                    'subtotal' => $item->subtotal,
                ]);
                if ($checkoutSource['source'] === 'cart' && $item->cartItem) {
                    $item->cartItem->delete();
                }
            }

            return $pesanan;
        });

        $redirectRoute = $pesanan->metode_pembayaran === Pesanan::METODE_COD
            ? route('pesanan.index')
            : route('pesanan.show', $pesanan);

        $message = $pesanan->metode_pembayaran === Pesanan::METODE_COD
            ? 'Pesanan berhasil dibuat dengan kode ' . $pesanan->kode_pesanan . '.'
            : 'Pesanan berhasil dibuat. Selesaikan pembayaran untuk pesanan ' . $pesanan->kode_pesanan . '.';
        $productSummary = $this->formatCheckoutProductSummary($items);
        $firstProduct = $items->first()?->produk;
        $this->recordActivity(
            'buy',
            'pesanan',
            'Pengguna membeli ' . $productSummary . ' pada pesanan ' . $pesanan->kode_pesanan . '.',
            produk: $firstProduct
        );

        return redirect($redirectRoute)->with('success', $message);
    }

    private function resolveAlamatForCheckout(Request $request, User $user, array $validated): Alamat
    {
        if ($validated['alamat_mode'] === 'existing') {
            $address = $user->alamat()
                ->where('id_alamat', $validated['alamat_id'])
                ->first();

            if (! $address) {
                throw ValidationException::withMessages([
                    'alamat_id' => 'Alamat yang dipilih tidak ditemukan atau bukan milik akunmu.',
                ]);
            }

            return $address;
        }

        $newAddress = validator($request->all(), [
            'nama_penerima' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:70'],
            'kota' => ['required', 'string', 'max:100'],
            'kode_pos' => ['required', 'string', 'max:10'],
        ])->validate();

        return Alamat::create([
            'id_user' => $user->id_user,
            'nama_penerima' => $newAddress['nama_penerima'],
            'no_hp' => $newAddress['no_hp'],
            'alamat' => $newAddress['alamat'],
            'kota' => $newAddress['kota'],
            'kode_pos' => $newAddress['kode_pos'],
            'is_default' => false,
        ]);
    }

    private function resolveCheckoutSource(Request $request, User $user): array
    {
        if ($request->filled('produk_id')) {
            $qty = max(1, (int) $request->query('qty', 1));
            $product = Produk::with('kategori')
                ->active()
                ->where('id_produk', $request->query('produk_id'))
                ->first();

            if (! $product) {
                return [
                    'source' => 'direct',
                    'items' => collect(),
                    'product' => null,
                    'qty' => $qty,
                ];
            }

            return [
                'source' => 'direct',
                'items' => $this->makeDirectCheckoutItems($product, $qty),
                'product' => $product,
                'qty' => $qty,
            ];
        }

        return [
            'source' => 'cart',
            'items' => $user->keranjangItems()
                ->with('produk.kategori')
                ->get()
                ->map(fn (KeranjangItem $item) => CheckoutItem::fromCartItem($item)),
            'product' => null,
            'qty' => null,
        ];
    }

    private function resolveCheckoutSourceFromValidated(User $user, array $validated): array
    {
        if (($validated['checkout_source'] ?? 'cart') === 'direct') {
            $qty = max(1, (int) ($validated['qty'] ?? 1));
            $product = Produk::active()
                ->where('id_produk', $validated['produk_id'] ?? 0)
                ->first();

            return [
                'source' => 'direct',
                'items' => $product ? $this->makeDirectCheckoutItems($product, $qty) : collect(),
            ];
        }

        return [
            'source' => 'cart',
            'items' => $user->keranjangItems()
                ->with('produk')
                ->get()
                ->map(fn (KeranjangItem $item) => CheckoutItem::fromCartItem($item)),
        ];
    }

    private function makeDirectCheckoutItems(Produk $product, int $qty): Collection
    {
        return collect([
            CheckoutItem::fromProduct($product, $qty),
        ]);
    }

    private function makeOrderCode(): string
    {
        do {
            $code = 'P2L' . now()->format('ymdHi') . Str::upper(Str::random(2));
        } while (Pesanan::where('kode_pesanan', $code)->exists());

        return $code;
    }

    private function formatCheckoutProductSummary(Collection $items): string
    {
        return $items
            ->map(fn (CheckoutItem $item) => $item->produk->nama_produk . ' x' . $item->qty)
            ->join(', ');
    }

    private function redirectUnavailableCheckout(string $source): RedirectResponse
    {
        return redirect()->route($source === 'direct' ? 'produk.index' : 'keranjang.index');
    }
}
