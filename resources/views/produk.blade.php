<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Produk - P2L Hebat</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>

    @vite(['resources/css/app.css', 'resources/css/produk.css'])
</head>
<body class="app-page-body font-poppins produk-body">
@include('partials.login-success-toast')
@include('partials.register-success-toast')
@include('partials.action-success-toast')
@php
    $isAdminUser = auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin'], true);
    $isLoggedInUser = auth()->check() && ! $isAdminUser;
@endphp
<nav class="app-top-nav fixed top-0 left-0 w-full z-50 text-white">
    <div class="app-top-nav-inner flex items-center justify-between px-10 py-3 text-white">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-putih.png') }}" class="logo-img" alt="P2L Hebat">
            <span class="font-semibold text-lg">P2L Hebat</span>
        </a>

        <div class="absolute left-1/2 transform -translate-x-1/2 flex gap-12 font-medium nav-center-links">
            <a href="{{ route('beranda') }}" class="hover:text-white/80">Beranda</a>
            <a href="{{ route('produk.index') }}" class="border-b-2 border-white pb-1">Produk</a>
            @if($isAdminUser)
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white/80">Dashboard Admin</a>
            @elseif($isLoggedInUser)
                <a href="{{ route('profil.show') }}#alamat-section" class="hover:text-white/80">Alamat</a>
                <a href="{{ route('keranjang.index') }}" class="hover:text-white/80">Keranjang</a>
                <a href="{{ route('pesanan.index') }}" class="hover:text-white/80">Pesanan</a>
            @endif
        </div>

        <div class="flex items-center gap-4">
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
                <a href="{{ route('login') }}" class="btn-nav">Masuk</a>
                <a href="{{ route('register') }}" class="btn-nav">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

<main class="produk-page">
    <section class="produk-section">
        <div class="section-headline app-fade-up">
            <div>
                <h2>Katalog Produk</h2>
                <p>Pilih produk yang kamu butuhkan dengan kualitas menjanjikan</p>
            </div>
            <div class="section-summary">
                <span class="summary-pill" data-interactive-surface>{{ $produk->count() }} produk</span>
                <span class="summary-pill" data-interactive-surface>{{ $kategori->count() }} kategori</span>
                <span class="summary-pill" data-interactive-surface>
                    <a href="{{ $isAdminUser ? route('admin.dashboard') : ($isLoggedInUser ? route('keranjang.index') : route('login')) }}">
                        {{ $isAdminUser ? 'Kembali ke dashboard' : ($isLoggedInUser ? 'Lihat keranjang' : 'Masuk untuk belanja') }}
                    </a>
                </span>
            </div>
        </div>

        @if(session('error'))
            <div class="catalog-alert app-alert app-alert-error">{{ session('error') }}</div>
        @endif

        <div class="catalog-filter-stack">
            <div class="catalog-filter-card catalog-filter-card-main app-fade-up app-delay-1" data-interactive-surface>
                <div class="catalog-filter-main-head">
                    <div>
                        <span class="catalog-toolbar-label">Kategori Produk</span>
                        <p>Pilih kategori untuk melihat produk yang sesuai kebutuhanmu.</p>
                    </div>
                    <div class="catalog-filter-status">
                        <span class="catalog-status-pill">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ $selectedKategori?->nama_kategori ?? 'Semua kategori' }}
                        </span>
                        @if($selectedKategori)
                            <span class="catalog-status-pill">
                                <i class="fa-solid fa-tag"></i>
                                1 kategori aktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="catalog-category-inline">
                    <div class="catalog-category-inline-head">
                        <span class="catalog-category-inline-label">Kategori</span>
                        <p>Geser atau pilih cepat kategori produk:</p>
                    </div>

                    <div class="filter-stack filter-stack-inline" id="filterContainer">
                        <a href="{{ route('produk.index') }}" class="filter-chip {{ request('kategori') ? '' : 'active' }}">Semua Produk</a>
                        @foreach($kategori as $item)
                            <a href="{{ route('produk.kategori', $item->id_kategori) }}"
                               class="filter-chip {{ (string) request('kategori') === (string) $item->id_kategori ? 'active' : '' }}">
                                {{ $item->nama_kategori }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="catalog-shell app-fade-up app-delay-1">
            <div class="catalog-toolbar">
                <div class="catalog-intro">
                    <h3>Katalog Produk</h3>
                    <p>
                        Menampilkan <strong>{{ $produk->count() }}</strong> produk aktif
                        @if($selectedKategori)
                            dalam kategori <strong>{{ $selectedKategori->nama_kategori }}</strong>.
                        @else
                            yang siap dipilih pengguna.
                        @endif
                    </p>
                </div>

                <form action="{{ route('produk.index') }}" method="GET" class="catalog-search-form" data-catalog-search-form>
                    @if(request('kategori'))
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    @endif
                    <label class="catalog-search-field">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari produk atau kategori"
                            autocomplete="off"
                            data-catalog-search-input
                        >
                    </label>
                    @if($search !== '')
                        <a href="{{ request('kategori') ? route('produk.index', ['kategori' => request('kategori')]) : route('produk.index') }}" class="catalog-search-reset">Reset</a>
                    @endif
                </form>
            </div>

            <div class="product-grid">
                @forelse($produk as $item)
                    @php
                        $delayClass = match($loop->iteration % 4) {
                            1 => '',
                            2 => 'app-delay-1',
                            3 => 'app-delay-2',
                            default => 'app-delay-3',
                        };
                    @endphp
                    <article class="product-card app-fade-up {{ $delayClass }}" data-interactive-surface>
                        <div class="product-media-wrap">
                            <img src="{{ $item->gambar_produk_url }}" alt="{{ $item->nama_produk }}" class="product-image">
                            <span class="product-badge">{{ $item->kategori?->nama_kategori ?? 'Tanpa kategori' }}</span>
                        </div>

                        <div class="product-content">
                            <h3>{{ $item->nama_produk }}</h3>
                            <p class="product-category">{{ $item->kategori?->nama_kategori ?? 'Kategori belum diatur' }}</p>
                            <p class="product-price">{{ $item->harga_satuan_label }}</p>

                            <div class="product-actions stacked-actions">
                                @if($isAdminUser)
                                    <a href="{{ route('admin.dashboard') }}" class="btn-shop">
                                        <i class="fa-solid fa-chart-line"></i>
                                        Dashboard Admin
                                    </a>
                                @elseif($isLoggedInUser)
                                    <form action="{{ route('keranjang.store') }}" method="POST" class="add-cart-form" data-add-cart-form>
                                        @csrf
                                        <input type="hidden" name="produk_id" value="{{ $item->id_produk }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit" class="btn-shop" data-add-cart-button>
                                            <i class="fa-solid fa-cart-plus"></i>
                                            <span>Tambah ke Keranjang</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('checkout') }}" method="GET" class="add-cart-form">
                                        <input type="hidden" name="produk_id" value="{{ $item->id_produk }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit" class="btn-shop btn-shop-secondary">
                                            <i class="fa-solid fa-basket-shopping"></i>
                                            Checkout Sekarang
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login', ['intended_action' => 'cart', 'produk_id' => $item->id_produk, 'qty' => 1]) }}" class="btn-shop">
                                        <i class="fa-solid fa-cart-plus"></i>
                                        Tambah ke Keranjang
                                    </a>

                                    <a href="{{ route('login', ['intended_action' => 'checkout', 'produk_id' => $item->id_produk, 'qty' => 1]) }}" class="btn-shop btn-shop-secondary">
                                        <i class="fa-solid fa-basket-shopping"></i>
                                        Checkout Sekarang
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-products">
                        <div class="page-empty-icon">
                            <i class="fa-solid fa-seedling"></i>
                        </div>
                        <h3>Belum ada produk aktif</h3>
                        <p>Produk yang berstatus aktif akan tampil di sini. Coba pilih kategori lain atau tambahkan produk dari dashboard admin.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</main>

<script>
feather.replace();

const revealTargets = document.querySelectorAll('.app-fade-up');

if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.18 });

    revealTargets.forEach((element) => revealObserver.observe(element));
} else {
    revealTargets.forEach((element) => element.classList.add('is-visible'));
}

document.querySelectorAll('[data-interactive-surface]').forEach((element) => {
    element.addEventListener('pointermove', (event) => {
        const rect = element.getBoundingClientRect();
        const x = ((event.clientX - rect.left) / rect.width) * 100;
        const y = ((event.clientY - rect.top) / rect.height) * 100;

        element.style.setProperty('--pointer-x', `${x}%`);
        element.style.setProperty('--pointer-y', `${y}%`);
    });
});

(() => {
    const form = document.querySelector('[data-catalog-search-form]');
    const input = document.querySelector('[data-catalog-search-input]');

    if (!form || !input) {
        return;
    }

    let typingTimer;

    input.addEventListener('input', () => {
        window.clearTimeout(typingTimer);
        typingTimer = window.setTimeout(() => {
            form.requestSubmit();
        }, 550);
    });
})();

(() => {
    const forms = document.querySelectorAll('[data-add-cart-form]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!forms.length || !csrfToken) {
        return;
    }

    const showFloatingMessage = (message, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = type === 'error' ? 'app-alert app-alert-error catalog-floating-alert' : 'app-alert app-alert-success catalog-floating-alert';
        toast.textContent = message;
        document.body.appendChild(toast);

        window.setTimeout(() => {
            toast.classList.add('is-hiding');
            window.setTimeout(() => toast.remove(), 220);
        }, 2200);
    };

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('[data-add-cart-button]');
            const label = button?.querySelector('span');

            if (!button || !label) {
                return;
            }

            button.disabled = true;
            form.classList.add('is-submitting');
            const originalLabel = label.textContent;
            label.textContent = 'Menambahkan...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload.message || 'Produk belum bisa ditambahkan ke keranjang.');
                }

                label.textContent = `Masuk (${payload.item.qty} ${payload.item.satuan_label})`;
                showFloatingMessage(payload.message);
                window.setTimeout(() => {
                    label.textContent = originalLabel;
                }, 1600);
            } catch (error) {
                label.textContent = originalLabel;
                showFloatingMessage(error.message, 'error');
            } finally {
                button.disabled = false;
                form.classList.remove('is-submitting');
            }
        });
    });
})();
</script>

@include('partials.logout-modal')
</body>
</html>
