<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\KeranjangItem;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show(Request $request)
    {
        if (Auth::check()) {
            return redirect()->to($this->redirectPathFor($this->authUser()));
        }

        $request->session()->put('login_intended_action', $request->query('intended_action'));
        $request->session()->put('login_produk_id', $request->query('produk_id'));
        $request->session()->put('login_qty', $request->query('qty', 1));

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user && empty($user->password) && ! empty($user->google_id)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Akun ini terdaftar via Google. Login dulu dengan Google lalu atur password manual dari halaman profil.');
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $message = $user && ! empty($user->google_id)
                ? 'Password tidak cocok. Jika akun ini dibuat lewat Google, login dulu dengan Google lalu buat password manual di profil.'
                : 'Email atau password salah.';

            return back()
                ->withInput($request->only('email'))
                ->with('error', $message);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('auth_provider', 'manual');

        $message = in_array($user->role, ['super_admin', 'admin'], true)
            ? 'Login berhasil. Anda masuk sebagai admin.'
            : 'Login berhasil.';

        return $this->redirectAfterLogin($request, $user, $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/beranda')->with('logout_success', 'Logout berhasil!');
    }

    public function fallbackLogout()
    {
        return redirect('/beranda')->with('error', 'Silakan gunakan tombol untuk keluar/logout secara aman.');
    }

    protected function redirectPathFor(User $user): string
    {
        return in_array($user->role, ['super_admin', 'admin'], true)
            ? '/admin'
            : '/produk';
    }

    protected function redirectAfterLogin(Request $request, User $user, string $message): RedirectResponse
    {
        if (in_array($user->role, ['super_admin', 'admin'], true)) {
            $this->clearIntendedTransaction($request);

            return redirect($this->redirectPathFor($user))->with('login_success', $message);
        }

        $action = (string) $request->session()->pull('login_intended_action', '');
        $produkId = (int) $request->session()->pull('login_produk_id', 0);
        $qty = max(1, (int) $request->session()->pull('login_qty', 1));

        if ($action === 'checkout' && $produkId > 0) {
            return redirect()
                ->route('checkout', ['produk_id' => $produkId, 'qty' => $qty])
                ->with('login_success', $message);
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

            return redirect()
                ->route('keranjang.index')
                ->with('login_success', $message)
                ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
        }

        return redirect($this->redirectPathFor($user))->with('login_success', $message);
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
