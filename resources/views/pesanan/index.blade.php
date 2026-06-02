<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - P2L Hebat</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/pesanan.css'])
</head>
<body class="app-page-body pesanan-page-body">
@include('partials.action-success-toast')
@php
    $isAdminUser = auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin'], true);
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
                <span class="section-kicker">Riwayat Pesanan</span>
                <h1>Pesananmu tersimpan rapi</h1>
                <p>Lihat daftar pesanan yang pernah dibuat, status prosesnya, dan detail produk yang sudah kamu checkout.</p>
            </div>
            <div class="pesanan-stats">
                <span>{{ $orders->count() }} pesanan</span>
            </div>
        </div>

        <div class="pesanan-overview-strip app-fade-up app-delay-1">
            <div class="pesanan-overview-card">
                <strong>{{ $orders->where('status_pesanan', \App\Models\Pesanan::STATUS_MENUNGGU_PEMBAYARAN)->count() }}</strong>
                <p>Menunggu pembayaran</p>
            </div>
            <div class="pesanan-overview-card">
                <strong>{{ $orders->where('status_pesanan', \App\Models\Pesanan::STATUS_DIPROSES)->count() }}</strong>
                <p>Sedang diproses</p>
            </div>
            <div class="pesanan-overview-card">
                <strong>{{ $orders->where('status_pesanan', \App\Models\Pesanan::STATUS_DIKIRIM)->count() }}</strong>
                <p>Sedang dikirim</p>
            </div>
            <div class="pesanan-overview-card">
                <strong>{{ $orders->where('status_pesanan', \App\Models\Pesanan::STATUS_SELESAI)->count() }}</strong>
                <p>Pesanan selesai</p>
            </div>
        </div>

        @if(session('error'))
            <div class="pesanan-alert app-alert app-alert-error">{{ session('error') }}</div>
        @endif

        @if($orders->isEmpty())
            <div class="pesanan-empty-state">
                <div class="page-empty-icon"><i class="fa-solid fa-receipt"></i></div>
                <h2>Belum ada pesanan</h2>
                <p>Setelah checkout berhasil, riwayat pesananmu akan muncul di halaman ini.</p>
                <a href="{{ route('produk.index') }}" class="btn-primary-pill">Kembali ke Produk</a>
            </div>
        @else
            <div class="pesanan-stack">
                @foreach($orders as $order)
                    <article class="pesanan-card app-fade-up app-delay-1">
                        <div class="pesanan-head">
                            <div>
                                <span class="pesanan-kode">{{ $order->kode_pesanan }}</span>
                                <h2>{{ optional($order->created_at)->format('d M Y, H:i') }}</h2>
                            </div>
                            <div class="pesanan-head-right">
                                <span class="pesanan-status pesanan-status-{{ $order->status_css }}">{{ $order->status_label }}</span>
                                <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        <div class="pesanan-meta-grid">
                            <div>
                                <p>Metode Pembayaran</p>
                                <strong>{{ $order->metode_pembayaran_label }}</strong>
                            </div>
                            <div>
                                <p>Tujuan Pengiriman</p>
                                <strong>{{ $order->alamat?->kota ?? '-' }}</strong>
                            </div>
                            <div>
                                <p>Penerima</p>
                                <strong>{{ $order->alamat?->nama_penerima ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="pesanan-mini-timeline">
                            <div class="pesanan-mini-step is-done">Checkout dibuat</div>
                            <div class="pesanan-mini-step {{ $order->hasReachedStatus(\App\Models\Pesanan::STATUS_DIPROSES) ? 'is-done' : '' }}">Diproses</div>
                            <div class="pesanan-mini-step {{ $order->hasReachedStatus(\App\Models\Pesanan::STATUS_DIKIRIM) ? 'is-done' : '' }}">Dikirim</div>
                            <div class="pesanan-mini-step {{ $order->hasReachedStatus(\App\Models\Pesanan::STATUS_SELESAI) ? 'is-done' : '' }}">Selesai</div>
                        </div>

                        @if($order->is_cancelled)
                            <div class="pesanan-alert app-alert app-alert-error">Pesanan ini dibatalkan.</div>
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

                        <div class="pesanan-actions">
                            <a href="{{ route('pesanan.show', $order) }}" class="pesanan-action-link">Lihat Detail Pesanan</a>
                            <span class="pesanan-ui-chip">{{ $order->detail->count() }} produk</span>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</main>
</body>
</html>
