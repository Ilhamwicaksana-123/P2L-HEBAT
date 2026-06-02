<?php

use App\Http\Controllers\AlamatController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProdukController::class, 'index'])->name('home');
Route::get('/beranda', [BerandaController::class, 'beranda'])->name('beranda');
Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/produk/kategori/{id}', [ProdukController::class, 'kategori'])->name('produk.kategori');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/logout', [LoginController::class, 'fallbackLogout'])
    ->name('logout.fallback');

Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profil.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profil.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profil.password.update');
});

Route::middleware(['auth', 'user.role'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::patch('/keranjang/{cart}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{cart}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{order}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan/{order}/bayar', [PesananController::class, 'pay'])->name('pesanan.bayar');

    Route::post('/alamat/store', [AlamatController::class, 'store'])->name('alamat.store');
    Route::patch('/alamat/{alamat}', [AlamatController::class, 'update'])->name('alamat.update');
    Route::delete('/alamat/{alamat}', [AlamatController::class, 'destroy'])->name('alamat.destroy');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('kategoris', App\Http\Controllers\Admin\KategoriController::class)->except(['show']);
        Route::resource('produks', App\Http\Controllers\Admin\ProdukController::class)->except(['show']);
        Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
        Route::get('metode-pembayaran', [App\Http\Controllers\Admin\PaymentMethodController::class, 'index'])
            ->name('payment-methods.index');
        Route::patch('metode-pembayaran', [App\Http\Controllers\Admin\PaymentMethodController::class, 'update'])
            ->name('payment-methods.update');
        Route::resource('pesanan', App\Http\Controllers\Admin\PesananController::class)
            ->only(['index', 'show'])
            ->names('pesanan');
        Route::get('laporan', [App\Http\Controllers\Admin\LaporanController::class, 'index'])
            ->name('laporan.index');
        Route::get('laporan/cetak', [App\Http\Controllers\Admin\LaporanController::class, 'print'])
            ->name('laporan.print');
        Route::get('laporan/export-pdf', [App\Http\Controllers\Admin\LaporanController::class, 'exportPdf'])
            ->name('laporan.export-pdf');
        Route::patch('pesanan/{pesanan}/status', [App\Http\Controllers\Admin\PesananController::class, 'updateStatus'])
            ->name('pesanan.update-status');
    });

Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');

Route::post('/midtrans/notification', MidtransWebhookController::class)
    ->name('midtrans.notification');
