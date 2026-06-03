<?php

namespace App\Http\Controllers;

use App\Models\KeranjangItem;
use App\Models\Produk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public function index(): View
    {
        $cartItems = $this->authUser()
            ->keranjangItems()
            ->with('produk.kategori')
            ->latest()
            ->get();

        $subtotal = $cartItems->sum(fn (KeranjangItem $item) => $item->subtotal);
        $hasUnavailableItems = $cartItems->contains(function (KeranjangItem $item) {
            return ! $item->produk
                || ! $item->produk->is_active;
        });

        return view('keranjang.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'hasUnavailableItems' => $hasUnavailableItems,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'produk_id' => ['required', 'integer', 'exists:ms_produk,id_produk'],
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $produk = Produk::query()
            ->where('id_produk', $validated['produk_id'])
            ->active()
            ->firstOrFail();

        $qtyTambahan = (int) ($validated['qty'] ?? 1);

        $cartItem = KeranjangItem::firstOrNew([
            'id_user' => Auth::id(),
            'id_produk' => $produk->id_produk,
        ]);

        $qtyBaru = ($cartItem->exists ? $cartItem->qty : 0) + $qtyTambahan;

        $cartItem->fill([
            'qty' => $qtyBaru,
            'harga_satuan' => $produk->harga_produk,
        ]);
        $cartItem->save();
        $this->recordActivity(
            'add_to_cart',
            'keranjang',
            'Pengguna menambahkan ' . $produk->nama_produk . ' ke keranjang.',
            produk: $produk
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'cart' => $this->buildCartMeta(),
                'item' => [
                    'id' => $cartItem->id_keranjang_item,
                    'qty' => (int) $cartItem->qty,
                    'satuan_label' => strtolower($produk->satuan_label),
                    'subtotal' => (float) $cartItem->subtotal,
                    'subtotal_label' => $this->formatRupiah($cartItem->subtotal),
                ],
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $cart): RedirectResponse|JsonResponse
    {
        $cartItem = KeranjangItem::findOrFail($cart);
        abort_unless((int) $cartItem->id_user === (int) Auth::id(), 403);

        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $produk = $cartItem->produk;

        if (! $produk || ! $produk->is_active) {
            return $this->errorResponse($request, 'Produk ini sudah tidak aktif dan tidak bisa diubah dari keranjang.');
        }

        $cartItem->update([
            'qty' => (int) $validated['qty'],
            'harga_satuan' => $produk->harga_produk,
        ]);
        $this->recordActivity(
            'update',
            'keranjang',
            'Pengguna memperbarui jumlah ' . $produk->nama_produk . ' di keranjang.',
            produk: $produk
        );

        if ($request->expectsJson()) {
            $cartItem->refresh();

            return response()->json([
                'message' => 'Jumlah produk di keranjang berhasil diperbarui.',
                'cart' => $this->buildCartMeta(),
                'item' => [
                    'id' => $cartItem->id_keranjang_item,
                    'qty' => (int) $cartItem->qty,
                    'satuan_label' => strtolower($produk->satuan_label),
                    'subtotal' => (float) $cartItem->subtotal,
                    'subtotal_label' => $this->formatRupiah($cartItem->subtotal),
                ],
            ]);
        }

        return back()->with('success', 'Jumlah produk di keranjang berhasil diperbarui.');
    }

    public function destroy(int $cart): RedirectResponse
    {
        $cartItem = KeranjangItem::findOrFail($cart);
        abort_unless((int) $cartItem->id_user === (int) Auth::id(), 403);

        $produk = $cartItem->produk;
        $cartItem->delete();
        $this->recordActivity(
            'delete',
            'keranjang',
            'Pengguna menghapus ' . ($produk?->nama_produk ?? 'produk') . ' dari keranjang.',
            produk: $produk
        );

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    private function buildCartMeta(): array
    {
        $cartItems = $this->authUser()
            ->keranjangItems()
            ->get();

        $totalItems = (int) $cartItems->sum('qty');
        $subtotal = (float) $cartItems->sum(fn (KeranjangItem $item) => $item->subtotal);

        return [
            'line_count' => (int) $cartItems->count(),
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'subtotal_label' => $this->formatRupiah($subtotal),
        ];
    }

    private function formatRupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    private function errorResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], 422);
        }

        return back()->with('error', $message);
    }
}
