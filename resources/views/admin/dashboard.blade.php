@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('hide_admin_header', true)

@section('content')
<section class="admin-hero">
    <div>
        <span class="hero-pill">P2L Hebat</span>
        <h2>Pusat pengelolaan data Pekarangan Pangan Lestari.</h2>
        <p>Gunakan dashboard ini untuk memantau katalog produk, kategori, dan pesanan agar pengelolaan P2L tetap rapi dan terarah.</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('admin.produks.create') }}" class="btn btn-primary">Tambah produk</a>
        <a href="{{ route('admin.kategoris.create') }}" class="btn btn-secondary">Tambah kategori</a>
    </div>
</section>

<div class="admin-grid admin-grid-cards">
    <div class="admin-card stat-card">
        <span class="stat-label">Total pengguna</span>
        <strong class="stat-value">{{ $totalUsers }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Total kategori</span>
        <strong class="stat-value">{{ $totalKategori }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Total produk</span>
        <strong class="stat-value">{{ $totalProduk }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Produk aktif</span>
        <strong class="stat-value">{{ $produkAktif }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Transaksi {{ $currentYear }}</span>
        <strong class="stat-value">{{ $totalPesanan }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Omset {{ $currentYear }}</span>
        <strong class="stat-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</strong>
    </div>
    <div class="admin-card stat-card">
        <span class="stat-label">Menunggu bayar</span>
        <strong class="stat-value">{{ $pesananMenunggu }}</strong>
    </div>
</div>

<div class="admin-card dashboard-section-card">
    <div class="section-head">
        <div>
            <h2>Produk terbaru</h2>
            <p>Periksa produk yang baru ditambahkan untuk memastikan nama, harga, satuan, kategori, dan status tampilnya sudah sesuai.</p>
        </div>
        <a href="{{ route('admin.produks.index') }}" class="btn btn-secondary">Kelola produk</a>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Satuan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($produkTerbaru as $item)
                    <tr>
                        <td>{{ $item->nama_produk }}</td>
                        <td>{{ $item->kategori?->nama_kategori ?? '-' }}</td>
                        <td>Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</td>
                        <td>{{ $item->satuan_label }}</td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-muted' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">Belum ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card dashboard-section-card">
    <div class="section-head">
        <div>
            <h2>Pesanan terbaru</h2>
            <p>Pantau pesanan yang baru masuk agar proses verifikasi dan penanganannya bisa dilakukan lebih cepat.</p>
        </div>
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary">Lihat semua pesanan</a>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Kota</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesananTerbaru as $item)
                    <tr>
                        <td>{{ $item->kode_pesanan }}</td>
                        <td>{{ $item->user?->nama ?? '-' }}</td>
                        <td>{{ $item->alamat?->kota ?? '-' }}</td>
                        <td>{{ $item->metode_pembayaran_label }}</td>
                        <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-status-{{ $item->status_css }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada data pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
