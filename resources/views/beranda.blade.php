<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - P2L Hebat</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/beranda.css'])

    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="app-page-body font-poppins flex flex-col min-h-screen">
@include('partials.logout-success-toast')
@php
    $isAdminUser = auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin'], true);
@endphp
<nav class="app-top-nav fixed top-0 left-0 w-full z-50 text-white">
    <div class="app-top-nav-inner flex items-center justify-between px-10 py-3 text-white">
        <a href="/" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-putih.png') }}" class="logo-img" alt="P2L Hebat">
            <span class="text-lg font-semibold text-white">P2L Hebat</span>
        </a>

        <div class="absolute left-1/2 transform -translate-x-1/2 flex gap-14 md:gap-20 font-medium nav-center-links">
            <a href="/beranda" class="px-3 pb-1 border-b-2 border-white">Beranda</a>
            <a href="/produk" class="px-3 hover:text-white/80 transition">Produk</a>
            @if($isAdminUser)
                <a href="{{ route('admin.dashboard') }}" class="px-3 hover:text-white/80 transition">Dashboard Admin</a>
            @elseif(auth()->check())
                <a href="{{ route('profil.show') }}#alamat-section" class="px-3 hover:text-white/80 transition">Alamat</a>
            @endif
        </div>

        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('profil.show') }}" class="nav-avatar-link" title="Buka profil">
                    <span class="nav-greeting">Hai, {{ Auth::user()->nama ?? 'User' }}</span>
                    <img src="{{ Auth::user()->photo_url }}" class="nav-avatar-image" alt="{{ Auth::user()->nama ?? 'User' }}">
                </a>

                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display:none;">
                    @csrf
                </form>

                <a href="#" class="logout-nav-link" data-logout-trigger data-logout-target="logout-form" aria-label="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            @else
                <a href="/login" class="btn-nav">Masuk</a>
                <a href="/register" class="btn-nav">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

<main class="flex-grow">
<header class="relative h-screen hero-bg hero-screen pt-24">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="hero-orb hero-orb-left"></div>
    <div class="hero-orb hero-orb-right"></div>

    <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4">
        <p class="fade-up delay-1 mb-2 hero-tag hero-static-text">
            P2L - Pekarangan Pangan Lestari
        </p>

        <h1 class="fade-up delay-2 text-5xl md:text-7xl font-playfair font-bold mb-6 hero-static-text">
            SEGAR & BERKUALITAS
        </h1>

        <p class="fade-up delay-3 mb-10 text-lg hero-static-text">
            Siap memenuhi kebutuhan nutrisi harianmu dengan produk lokal terbaik
        </p>

        @auth
        <a href="{{ $isAdminUser ? route('admin.dashboard') : route('produk.index') }}" class="btn-shop hero-btn fade-up">
            <span>{{ $isAdminUser ? 'Buka Dashboard Admin' : 'Belanja Sekarang' }}</span>
            <i class="fa-solid {{ $isAdminUser ? 'fa-chart-line' : 'fa-cart-shopping' }} hero-btn-icon"></i>
        </a>
        @else
        <a href="{{ route('produk.index') }}" class="btn-shop hero-btn fade-up">
            <span>Belanja Sekarang</span>
            <i class="fa-solid fa-cart-shopping hero-btn-icon"></i>
        </a>
        @endauth
    </div>
</header>

<section class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center">
        <div class="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">
                Tentang P2L
            </h2>

            <p class="text-gray-600 leading-relaxed">
                Pekarangan Pangan Lestari (P2L) menyediakan produk segar langsung dari petani lokal
                dengan kualitas terbaik untuk memenuhi kebutuhan nutrisi harian masyarakat.
            </p>

            <p class="text-gray-600 mt-3">
                Kami memastikan setiap produk tetap segar, sehat, dan terjangkau.
            </p>
        </div>

        <div class="fade-up delay-1 flex justify-center">
            <img src="{{ asset('images/tentang.jpeg') }}"
                 class="rounded-2xl shadow-xl w-80"
                 alt="Tentang P2L">
        </div>
    </div>
</section>

<section class="bg-gray-50 py-20">
    <div class="max-w-6xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-10 fade-up">
            Kegiatan P2L
        </h2>

        <div class="grid md:grid-cols-3 gap-6">
            <img src="{{ asset('images/kegiatan1.jpeg') }}" class="gallery-img fade-up" alt="Kegiatan P2L 1">
            <img src="{{ asset('images/kegiatan2.jpeg') }}" class="gallery-img fade-up delay-1" alt="Kegiatan P2L 2">
            <img src="{{ asset('images/kegiatan3.jpeg') }}" class="gallery-img fade-up delay-2" alt="Kegiatan P2L 3">
        </div>
    </div>
</section>
</main>

<footer class="site-footer text-white">
    <div class="footer-overlay"></div>

    <div class="max-w-7xl mx-auto px-6 md:px-12 py-14 relative z-10">
        <div class="footer-top fade-up">
            <div>
                <span class="footer-pill">P2L Hebat</span>
                <h2 class="footer-heading">Segar dari petani lokal, hadir untuk kebutuhan harianmu.</h2>
                <p class="footer-subtext">
                    Kami percaya bahan pangan yang baik dimulai dari sumber yang baik.
                    P2L Hebat hadir untuk menghubungkan hasil panen lokal dengan keluarga Indonesia.
                </p>
            </div>

            @auth
                <a href="{{ $isAdminUser ? route('admin.dashboard') : route('produk.index') }}" class="footer-cta">
                    {{ $isAdminUser ? 'Dashboard Admin' : 'Lihat Katalog' }}
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="footer-cta">
                    Mulai Belanja
                    <i class="fa-solid fa-arrow-right-long"></i>
                </a>
            @endauth
        </div>

        <div class="footer-grid">
            <div class="footer-brand-block fade-up delay-1">
                <div class="footer-brand-line">
                    <img src="{{ asset('images/logo-putih.png') }}" class="footer-logo" alt="Logo P2L Hebat">
                    <div>
                        <h3>P2L Hebat</h3>
                        <p class="footer-brand-tag">Pekarangan Pangan Lestari</p>
                    </div>
                </div>
                <p class="footer-brand-copy">
                    Solusi pangan segar yang mengangkat hasil pekarangan dan petani lokal agar lebih dekat dengan kebutuhan keluarga setiap hari.
                </p>
            </div>

            <div class="footer-links-block fade-up delay-2">
                <span class="footer-mini-title">Menu </span>
                <ul class="footer-quick-links">
                    <li><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li><a href="{{ route('produk.index') }}">Produk</a></li>
                    @auth
                        <li><a href="{{ route('keranjang.index') }}">Keranjang</a></li>
                        <li><a href="{{ route('pesanan.index') }}">Pesanan</a></li>
                    @endauth
                </ul>
            </div>

            <div class="footer-social-block fade-up delay-3">
                <span class="footer-mini-title">Ikuti Kami</span>
                <div class="footer-social-list">
                    <a href="mailto:p2l@gmail.com" class="footer-social-link" aria-label="Email P2L Hebat">
                        <i class="fa-regular fa-envelope"></i>
                    </a>
                    <a href="https://wa.me/6281359348245" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="WhatsApp P2L Hebat">
                        <i class="fa-brands fa-whatsapp"></i>
                    </a>
                    <a href="https://maps.app.goo.gl/KvjbnaaRoG3jpb6Y8" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="Lokasi P2L Hebat">
                        <i class="fa-solid fa-location-dot"></i>
                    </a>
                </div>
            </div>

            <div class="footer-contact-block fade-up delay-4">
                <span class="footer-mini-title">Hubungi Kami</span>
                <ul class="footer-contact-list">
                    <li>
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Madiun, Jawa Timur</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-phone"></i>
                        <span>081359348245</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-clock"></i>
                        <span>Setiap hari, 08.00 - 20.00 WIB</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom fade-up delay-2">
            <span>&copy; {{ date('Y') }} P2L Hebat</span>
            <span>Segar, lokal, dan lebih dekat dengan kebutuhanmu</span>
            <span class="footer-status-pill">Status: Online</span>
        </div>
    </div>
</footer>

<script>
feather.replace();

const elements = document.querySelectorAll('.fade-up');

const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show');
        } else {
            entry.target.classList.remove('show');
        }
    });
}, { threshold: 0.2 });

elements.forEach(el => observer.observe(el));
</script>

@include('partials.logout-modal')

</body>
</html>
