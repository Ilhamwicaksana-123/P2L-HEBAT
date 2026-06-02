<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - P2L Hebat</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/pesanan.css'])
</head>
<body class="app-page-body pesanan-page-body">
@include('partials.action-success-toast')
@php
    $isAdminUser = auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin'], true);
    $latestTransaction = $order->transaksi->sortByDesc('id_transaksi')->first();
@endphp
<nav class="app-top-nav fixed top-0 left-0 w-full z-50 text-white">
    <div class="app-top-nav-inner flex items-center justify-between px-10 py-3 text-white">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-putih.png') }}" class="logo-img" alt="P2L Hebat">
            <span class="font-semibold text-lg">P2L Hebat</span>
        </a>

        <div class="absolute left-1/2 transform -translate-x-1/2 flex gap-12 font-medium nav-center-links">
            <a href="{{ route('beranda') }}" class="hover:text-white/80">Beranda</a>
            <a href="{{ route('produk.index') }}" class="hover:text-white/80">Produk</a>
            @if($isAdminUser)
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white/80">Dashboard Admin</a>
            @else
                <a href="{{ route('profil.show') }}#alamat-section" class="hover:text-white/80">Alamat</a>
                <a href="{{ route('keranjang.index') }}" class="hover:text-white/80">Keranjang</a>
                <a href="{{ route('pesanan.index') }}" class="border-b-2 border-white pb-1">Pesanan</a>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('profil.show') }}" class="nav-avatar-link" title="Buka profil">
                <span class="nav-greeting">Hai, {{ Auth::user()->nama ?? 'User' }}</span>
                <img src="{{ Auth::user()->photo_url }}" class="nav-avatar-image" alt="{{ Auth::user()->nama ?? 'User' }}">
            </a>
        </div>
    </div>
</nav>

<main class="pesanan-page">
    <section class="pesanan-section">
        <div class="pesanan-heading app-fade-up">
            <div>
                <span class="section-kicker">Detail Pesanan</span>
                <h1>{{ $order->kode_pesanan }}</h1>
                <p>Lihat status pesanan, tujuan pengiriman, dan ringkasan belanja pada satu halaman yang lebih jelas.</p>
            </div>
            <div class="pesanan-stats">
                <span>{{ $order->status_label }}</span>
            </div>
        </div>

        @if(session('error'))
            <div class="pesanan-alert app-alert app-alert-error">{{ session('error') }}</div>
        @endif

        <div class="pesanan-stack">
            <article class="pesanan-card app-fade-up app-delay-1">
                <div class="pesanan-head">
                    <div>
                        <span class="pesanan-kode">{{ $order->kode_pesanan }}</span>
                        <h2>Total Rp {{ number_format($order->total_harga, 0, ',', '.') }}</h2>
                    </div>
                    <div class="pesanan-head-right">
                        <span class="pesanan-status pesanan-status-{{ $order->status_css }}">{{ $order->status_label }}</span>
                        <strong>{{ $order->metode_pembayaran_label }}</strong>
                    </div>
                </div>

                <div class="pesanan-detail-hero">
                    <div class="pesanan-detail-hero-card">
                        <span>Status Saat Ini</span>
                        <strong>{{ $order->status_label }}</strong>
                    </div>
                    <div class="pesanan-detail-hero-card">
                        <span>Tanggal Pesan</span>
                        <strong>{{ optional($order->created_at)->format('d M Y, H:i') }}</strong>
                    </div>
                    <div class="pesanan-detail-hero-card">
                        <span>Total Produk</span>
                        <strong>{{ $order->detail->count() }} produk</strong>
                    </div>
                </div>

                <div class="pesanan-meta-grid">
                    <div>
                        <p>Penerima</p>
                        <strong>{{ $order->alamat?->nama_penerima ?? '-' }}</strong>
                    </div>
                    <div>
                        <p>Nomor HP</p>
                        <strong>{{ $order->alamat?->no_hp ?? '-' }}</strong>
                    </div>
                    <div>
                        <p>Kota</p>
                        <strong>{{ $order->alamat?->kota ?? '-' }}</strong>
                    </div>
                </div>

                <div class="pesanan-address-note">
                    <i class="fa-solid fa-location-dot"></i>
                    <p>{{ $order->alamat?->alamat ?? 'Alamat pengiriman belum tersedia.' }}</p>
                </div>

                <div class="pesanan-mini-timeline pesanan-mini-timeline-detail">
                    <div class="pesanan-mini-step is-done">Pesanan dibuat</div>
                    <div class="pesanan-mini-step {{ $order->hasReachedStatus(\App\Models\Pesanan::STATUS_DIPROSES) ? 'is-done' : '' }}">Diproses</div>
                    <div class="pesanan-mini-step {{ $order->hasReachedStatus(\App\Models\Pesanan::STATUS_DIKIRIM) ? 'is-done' : '' }}">Dikirim</div>
                    <div class="pesanan-mini-step {{ $order->hasReachedStatus(\App\Models\Pesanan::STATUS_SELESAI) ? 'is-done' : '' }}">Selesai</div>
                </div>

                @if($order->is_cancelled)
                    <div class="pesanan-alert app-alert app-alert-error">Pesanan ini dibatalkan dan tidak akan diproses lebih lanjut.</div>
                @endif

                @if($order->can_be_paid)
                    <aside class="pembayaran-card">
                        <span class="pesanan-ui-chip">Midtrans Sandbox</span>
                        <h2>Selesaikan Pembayaran</h2>
                        <p class="pembayaran-card-copy">
                            Kamu akan diarahkan ke halaman simulasi pembayaran Midtrans. Setelah akun Midtrans production aktif, cukup ganti server key dan ubah mode production.
                        </p>

                        <div class="pembayaran-info-list">
                            <div class="pembayaran-info-item">
                                <span>Kode Gateway</span>
                                <strong>{{ $latestTransaction?->kode_order_gateway ?? 'Dibuat saat tombol bayar diklik' }}</strong>
                            </div>
                            <div class="pembayaran-info-item">
                                <span>Status Pembayaran</span>
                                <strong>{{ ucfirst($latestTransaction?->status_pembayaran ?? 'menunggu') }}</strong>
                            </div>
                            <div class="pembayaran-info-item">
                                <span>Total Tagihan</span>
                                <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <form action="{{ route('pesanan.bayar', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary-pill pesanan-bayar-button">
                                <i class="fa-solid fa-credit-card"></i>
                                Bayar dengan Midtrans Sandbox
                            </button>
                        </form>
                    </aside>
                @elseif($latestTransaction)
                    <aside class="pembayaran-card">
                        <span class="pesanan-ui-chip">Pembayaran</span>
                        <h2>Status Pembayaran</h2>
                        <div class="pembayaran-info-list">
                            <div class="pembayaran-info-item">
                                <span>Kode Gateway</span>
                                <strong>{{ $latestTransaction->kode_order_gateway }}</strong>
                            </div>
                            <div class="pembayaran-info-item">
                                <span>Status Pembayaran</span>
                                <strong>{{ ucfirst($latestTransaction->status_pembayaran) }}</strong>
                            </div>
                            <div class="pembayaran-info-item">
                                <span>Metode Gateway</span>
                                <strong>{{ $latestTransaction->payment_type ? strtoupper(str_replace('_', ' ', $latestTransaction->payment_type)) : '-' }}</strong>
                            </div>
                        </div>
                    </aside>
                @endif

                <div class="pesanan-detail-list">
                    @foreach($order->detail as $detail)
                        <div class="pesanan-detail-row">
                            <div>
                                <h3>{{ $detail->nama_produk }}</h3>
                                <p>{{ $detail->qty }} {{ strtolower($detail->satuan_label) }} x Rp {{ number_format($detail->harga_produk, 0, ',', '.') }} / {{ strtolower($detail->satuan_label) }}</p>
                            </div>
                            <strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach
                </div>

                <div class="pesanan-actions pesanan-actions-column">
                    <a href="{{ route('pesanan.index') }}" class="pesanan-action-link">Kembali ke Riwayat Pesanan</a>
                    <a href="{{ route('produk.index') }}" class="pesanan-action-link pesanan-action-secondary">Belanja Lagi</a>
                </div>
            </article>
        </div>
    </section>
</main>
</body>
</html>
