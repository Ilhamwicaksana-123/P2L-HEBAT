<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = now()->year;
        $yearlyOrders = Pesanan::query()
            ->whereYear('created_at', $currentYear);

        return view('admin.dashboard', [
            'currentYear' => $currentYear,
            'totalUsers' => User::count(),
            'totalKategori' => Kategori::count(),
            'totalProduk' => Produk::count(),
            'totalPesanan' => (clone $yearlyOrders)->count(),
            'totalOmzet' => (clone $yearlyOrders)
                ->where('status_pesanan', '!=', Pesanan::STATUS_BATAL)
                ->sum('total_harga'),
            'produkAktif' => Produk::active()->count(),
            'pesananMenunggu' => (clone $yearlyOrders)
                ->where('status_pesanan', Pesanan::STATUS_MENUNGGU_PEMBAYARAN)
                ->count(),
            'produkTerbaru' => Produk::with('kategori')
                ->latest()
                ->take(5)
                ->get(),
            'pesananTerbaru' => Pesanan::with(['user', 'alamat'])
                ->latest('id_pesanan')
                ->take(5)
                ->get(),
        ]);
    }
}
