<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Keranjang - P2L Hebat</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>

    @vite(['resources/css/app.css', 'resources/css/keranjang.css'])
</head>
<body class="app-page-body keranjang-page-body">
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
            <a href="{{ route('keranjang.index') }}" class="border-b-2 border-white pb-1">Keranjang</a>
            @if($isAdminUser)
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white/80">Dashboard Admin</a>
            @else
                <a href="{{ route('profil.show') }}#alamat-section" class="hover:text-white/80">Alamat</a>
                <a href="{{ route('pesanan.index') }}" class="hover:text-white/80">Pesanan</a>
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

<main class="keranjang-page">
    <section class="keranjang-section">
        <div class="keranjang-heading app-fade-up">
            <div>
                <span class="section-kicker">Keranjang Belanja</span>
                <h1>Siapkan pesananmu</h1>
                <p>Semua produk yang kamu pilih terkumpul di sini. Atur jumlah, cek total, lalu lanjut ke checkout saat sudah siap.</p>
            </div>
            <div class="keranjang-summary-badge" data-cart-summary-badge>
                <span data-cart-line-count>{{ $cartItems->count() }} item</span>
                <span data-cart-subtotal-badge>Subtotal Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
        </div>

        @if(session('error'))
            <div class="keranjang-alert app-alert app-alert-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="keranjang-alert app-alert app-alert-error">{{ $errors->first() }}</div>
        @endif

        @if($cartItems->isEmpty())
            <div class="keranjang-empty-state">
                <div class="page-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <h2>Keranjangmu masih kosong</h2>
                <p>Pilih produk dari katalog terlebih dahulu, lalu produk akan muncul di sini untuk diproses ke checkout.</p>
                <a href="{{ route('produk.index') }}" class="btn-primary-pill">Lihat Katalog</a>
            </div>
        @else
            <div class="keranjang-layout">
                <div class="keranjang-list-card app-fade-up app-delay-1">
                    @foreach($cartItems as $item)
                        @php
                            $isUnavailable = ! $item->produk
                                || ! $item->produk->is_active;
                        @endphp
                        <article class="keranjang-item-row" data-cart-row data-cart-id="{{ $item->id_keranjang_item }}">
                            <div class="keranjang-item-media">
                                <img src="{{ $item->produk?->gambar_produk_url }}" alt="{{ $item->produk->nama_produk ?? 'Produk' }}">
                            </div>

                            <div class="keranjang-item-copy">
                                <span class="keranjang-item-kategori">{{ $item->produk?->kategori?->nama_kategori ?? 'Tanpa kategori' }}</span>
                                <h3>{{ $item->produk->nama_produk ?? 'Produk tidak tersedia' }}</h3>
                                <p>Harga satuan Rp {{ number_format($item->harga_satuan, 0, ',', '.') }} / {{ strtolower($item->produk?->satuan_label ?? 'kg') }}</p>
                                <div class="keranjang-item-pills">
                                    <span data-cart-item-pill>{{ $item->qty }} {{ strtolower($item->produk?->satuan_label ?? 'kg') }} dipilih</span>
                                    @if($isUnavailable)
                                        <span class="keranjang-item-pill-warning">Produk nonaktif</span>
                                    @endif
                                </div>
                            </div>

                            <div class="keranjang-item-actions">
                                <form action="{{ route('keranjang.update', $item) }}" method="POST" class="keranjang-qty-form" data-cart-qty-form>
                                    @csrf
                                    @method('PATCH')
                                    <label for="qty-{{ $item->id_keranjang_item }}">Jumlah</label>
                                    <div class="keranjang-qty-inline">
                                        <input
                                            id="qty-{{ $item->id_keranjang_item }}"
                                            type="number"
                                            name="qty"
                                            min="1"
                                            value="{{ $item->qty }}"
                                            data-cart-qty-input
                                            @disabled($isUnavailable)
                                        >
                                    </div>
                                </form>

                                <div class="keranjang-item-subtotal" data-cart-item-subtotal>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>

                                <form action="{{ route('keranjang.destroy', $item) }}" method="POST" id="cart-remove-form-{{ $item->id_keranjang_item }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        class="btn-keranjang-remove"
                                        data-action-trigger
                                        data-action-target="cart-remove-form-{{ $item->id_keranjang_item }}"
                                        data-action-kicker="Hapus Item"
                                        data-action-title="Hapus produk dari keranjang?"
                                        data-action-text="Produk ini akan dihapus dari keranjang belanjamu. Kamu masih bisa menambahkannya lagi nanti dari katalog."
                                        data-action-confirm="Ya, Hapus"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="keranjang-total-card app-fade-up app-delay-2">
                    <h2>Total belanja</h2>
                    <div class="app-summary-row">
                        <span>Total jumlah</span>
                        <strong data-cart-total-items>{{ $cartItems->sum('qty') }} satuan</strong>
                    </div>
                    <div class="app-summary-row">
                        <span>Subtotal</span>
                        <strong data-cart-subtotal>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </div>
                    <div class="app-summary-row app-summary-row-total">
                        <span>Total Checkout</span>
                        <strong data-cart-total>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </div>

                    <div class="summary-actions">
                        @if($hasUnavailableItems)
                            <span class="btn-primary-pill btn-primary-pill-disabled" aria-disabled="true">Lanjut ke Checkout</span>
                            <p class="keranjang-warning-note">Hapus dulu produk yang sudah nonaktif sebelum checkout.</p>
                        @else
                            <a href="{{ route('checkout') }}" class="btn-primary-pill">Lanjut ke Checkout</a>
                        @endif
                        <a href="{{ route('produk.index') }}" class="app-secondary-link">Tambah Produk Lagi</a>
                    </div>

                    <div class="keranjang-tip-box">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <p>Produk di keranjang belum menjadi pesanan sampai kamu menyelesaikan checkout.</p>
                    </div>
                </aside>
            </div>
        @endif
    </section>
</main>

<script>
feather.replace();

(() => {
    const forms = document.querySelectorAll('[data-cart-qty-form]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        ?? document.querySelector('input[name="_token"]')?.value;

    if (!forms.length || !csrfToken) {
        return;
    }

    const summaryLineCount = document.querySelector('[data-cart-line-count]');
    const summaryBadgeSubtotal = document.querySelector('[data-cart-subtotal-badge]');
    const summaryTotalItems = document.querySelector('[data-cart-total-items]');
    const summarySubtotal = document.querySelector('[data-cart-subtotal]');
    const summaryTotal = document.querySelector('[data-cart-total]');

    const showFloatingMessage = (message, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = type === 'error' ? 'app-alert app-alert-error cart-floating-alert' : 'app-alert app-alert-success cart-floating-alert';
        toast.textContent = message;
        document.body.appendChild(toast);

        window.setTimeout(() => {
            toast.classList.add('is-hiding');
            window.setTimeout(() => toast.remove(), 220);
        }, 2200);
    };

    const syncSummary = (cart) => {
        if (!cart) {
            return;
        }

        if (summaryLineCount) {
            summaryLineCount.textContent = `${cart.line_count} item`;
        }

        if (summaryBadgeSubtotal) {
            summaryBadgeSubtotal.textContent = `Subtotal ${cart.subtotal_label}`;
        }

        if (summaryTotalItems) {
            summaryTotalItems.textContent = `${cart.total_items} satuan`;
        }

        if (summarySubtotal) {
            summarySubtotal.textContent = cart.subtotal_label;
        }

        if (summaryTotal) {
            summaryTotal.textContent = cart.subtotal_label;
        }
    };

    const submitQtyUpdate = async (form) => {
        const input = form.querySelector('[data-cart-qty-input]');
        const row = form.closest('[data-cart-row]');
        const subtotalNode = row?.querySelector('[data-cart-item-subtotal]');
        const pillNode = row?.querySelector('[data-cart-item-pill]');

        if (!input || !row) {
            return;
        }

        const min = Number(input.min || 1);
        const max = Number(input.max || input.value || 1);
        const nextValue = Math.min(Math.max(Number(input.value || min), min), max);
        input.value = String(nextValue);

        row.classList.add('is-updating');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    _method: 'PATCH',
                    qty: nextValue,
                }),
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Jumlah produk tidak bisa diperbarui.');
            }

            if (subtotalNode) {
                subtotalNode.textContent = payload.item.subtotal_label;
            }

            if (pillNode) {
                pillNode.textContent = `${payload.item.qty} ${payload.item.satuan_label} dipilih`;
            }

            syncSummary(payload.cart);
        } catch (error) {
            showFloatingMessage(error.message, 'error');
        } finally {
            row.classList.remove('is-updating');
        }
    };

    forms.forEach((form) => {
        const input = form.querySelector('[data-cart-qty-input]');
        let timerId;

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            submitQtyUpdate(form);
        });

        input?.addEventListener('input', () => {
            window.clearTimeout(timerId);
            timerId = window.setTimeout(() => submitQtyUpdate(form), 450);
        });

        input?.addEventListener('change', () => {
            window.clearTimeout(timerId);
            submitQtyUpdate(form);
        });
    });
})();
</script>
@include('partials.action-confirm-modal')
</body>
</html>
