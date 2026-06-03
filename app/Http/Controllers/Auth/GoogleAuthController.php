<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\KeranjangItem;
use App\Models\Produk;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        $request->session()->put('login_intended_action', $request->query('intended_action'));
        $request->session()->put('login_produk_id', $request->query('produk_id'));
        $request->session()->put('login_qty', $request->query('qty', 1));

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user sudah ada berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                       ->orWhere('email', $googleUser->getEmail())
                       ->first();

            if ($user) {
                // Update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                // Buat user baru
                $user = User::create([
                    'nama' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'no_hp' => '',
                    'password' => null,
                    'role' => 'user',
                ]);
            }

            // Login user
            Auth::login($user);
            request()->session()->put('auth_provider', 'google');
            $this->recordActivity('login', 'auth', 'Pengguna login dengan Google.', $user);

            return $this->redirectAfterLogin(request(), $user, 'Login dengan Google berhasil!');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login dengan Google: ' . $e->getMessage());
        }
    }

    protected function redirectPathFor(User $user): string
    {
        return in_array($user->role, ['super_admin', 'admin'], true)
            ? '/admin'
            : '/produk';
    }

    protected function redirectAfterLogin(Request $request, User $user, string $message)
    {
        if (in_array($user->role, ['super_admin', 'admin'], true)) {
            $this->clearIntendedTransaction($request);

            return redirect($this->redirectPathFor($user))
                ->with('success', $message);
        }

        $action = (string) $request->session()->pull('login_intended_action', '');
        $produkId = (int) $request->session()->pull('login_produk_id', 0);
        $qty = max(1, (int) $request->session()->pull('login_qty', 1));

        if ($action === 'checkout' && $produkId > 0) {
            return redirect()
                ->route('checkout', ['produk_id' => $produkId, 'qty' => $qty])
                ->with('success', $message);
        }

        if ($action === 'cart' && $produkId > 0) {
            $produk = Produk::query()
                ->where('id_produk', $produkId)
                ->active()
                ->first();

            if (! $produk) {
                return redirect()
                    ->route('produk.index')
                    ->with('error', 'Produk tidak ditemukan atau sudah tidak aktif.');
            }

            $cartItem = KeranjangItem::firstOrNew([
                'id_user' => $user->id_user,
                'id_produk' => $produk->id_produk,
            ]);

            $qtyBaru = ($cartItem->exists ? (int) $cartItem->qty : 0) + $qty;

            $cartItem->fill([
                'qty' => $qtyBaru,
                'harga_satuan' => $produk->harga_produk,
            ]);
            $cartItem->save();
            $this->recordActivity('add_to_cart', 'keranjang', 'Pengguna menambahkan produk ke keranjang setelah login Google.', $user);

            return redirect()
                ->route('keranjang.index')
                ->with('success', 'Produk berhasil ditambahkan ke keranjang.')
                ->with('login_success', $message);
        }

        return redirect($this->redirectPathFor($user))
            ->with('success', $message);
    }

    protected function clearIntendedTransaction(Request $request): void
    {
        $request->session()->forget([
            'login_intended_action',
            'login_produk_id',
            'login_qty',
        ]);
    }
}
