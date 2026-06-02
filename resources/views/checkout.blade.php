<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - P2L Hebat</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/feather-icons"></script>

    @vite(['resources/css/app.css', 'resources/css/checkout.css'])
</head>
<body class="app-page-body checkout-page-body">
@php
    $isAdminUser = auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin'], true);
@endphp
<nav class="app-top-nav fixed top-0 left-0 w-full z-50 text-white">
    <div class="app-top-nav-inner checkout-nav-inner flex items-center justify-between px-10 py-3 text-white">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo-putih.png') }}" class="logo-img" alt="P2L Hebat">
            <span class="font-semibold text-lg">P2L Hebat</span>
        </a>

        <div class="absolute left-1/2 transform -translate-x-1/2 flex gap-12 font-medium nav-center-links">
            <a href="{{ route('beranda') }}" class="hover:text-white/80">Beranda</a>
            <a href="{{ route('produk.index') }}" class="hover:text-white/80">Produk</a>
            <a href="{{ route('profil.show') }}#alamat-section" class="hover:text-white/80">Alamat</a>
            <a href="{{ route('keranjang.index') }}" class="hover:text-white/80">Keranjang</a>
            <a href="{{ route('checkout') }}" class="border-b-2 border-white pb-1">Checkout</a>
            @if($isAdminUser)
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white/80">Dashboard Admin</a>
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

<main class="checkout-page">
    <section class="checkout-section">
        <div class="checkout-heading app-fade-up">
            <div>
                <span class="section-kicker">Checkout</span>
                <h1>Selesaikan pesananmu</h1>

            </div>
            <div class="checkout-heading-badge">
                <span>{{ $cartItems->count() }} produk dipilih</span>
            </div>
        </div>

        <div class="checkout-progress-strip app-fade-up app-delay-1">
            <div class="checkout-progress-step is-active">
                <span>1</span>
                <div>
                    <strong>Keranjang siap</strong>
                    <p>Produk sudah kamu pilih</p>
                </div>
            </div>
            <div class="checkout-progress-step is-active">
                <span>2</span>
                <div>
                    <strong>Isi checkout</strong>
                    <p>Lengkapi alamat dan pembayaran</p>
                </div>
            </div>
            <div class="checkout-progress-step">
                <span>3</span>
                <div>
                    <strong>Pesanan dibuat</strong>
                    <p>Status akan muncul di riwayat</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="checkout-alert app-alert app-alert-error">{{ $errors->first() }}</div>
        @endif

        <section class="checkout-unified-card app-fade-up app-delay-1">
            <div class="checkout-layout">
                <section class="checkout-form-card">
                    <div class="checkout-card-head">
                        <h2>Alamat Pengiriman</h2>
                    </div>

                    <form action="{{ route('checkout.process') }}" method="POST" class="checkout-form-grid">
                    @csrf
                    <input type="hidden" name="checkout_source" value="{{ $checkoutSource }}">
                    @if($checkoutSource === 'direct' && $directProduct)
                        <input type="hidden" name="produk_id" value="{{ $directProduct->id_produk }}">
                        <input type="hidden" name="qty" value="{{ $directQty }}">
                    @endif
                    @php
                        $selectedAddressId = old('alamat_mode', $alamat ? 'existing' : 'new') === 'existing'
                            ? (int) old('alamat_id', $alamat?->id_alamat)
                            : null;
                        $addressMode = old('alamat_mode', $alamat ? 'existing' : 'new');
                        $selectedAddress = $selectedAddressId
                            ? $addresses->firstWhere('id_alamat', $selectedAddressId)
                            : null;
                    @endphp

                    <div class="checkout-flow-card checkout-field-wide">
                        <div class="checkout-field">
                            <span>Pilih Alamat</span>

                            @if($addresses->isNotEmpty())
                                <div class="checkout-address-selector" data-address-selector>
                                    <div class="checkout-address-preview-shell">
                                        <div class="checkout-address-preview-label">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <span>Alamat aktif untuk pengiriman</span>
                                        </div>

                                        <div class="checkout-selected-address {{ $addressMode === 'existing' && $selectedAddress ? '' : 'is-hidden' }}" data-selected-address>
                                            <div class="checkout-selected-address-head">
                                                <div>
                                                    <strong>Alamat terpilih</strong>
                                                    <span>Dipakai untuk pesanan ini</span>
                                                </div>
                                                <a href="{{ route('profil.show') }}#alamat-section" class="checkout-inline-link">Kelola alamat</a>
                                            </div>
                                            <div class="checkout-selected-address-body" data-selected-address-body>
                                                @if($selectedAddress)
                                                    <strong>{{ $selectedAddress->nama_penerima }} - {{ $selectedAddress->no_hp }}</strong>
                                                    <p>{{ $selectedAddress->alamat }}, {{ $selectedAddress->kota }}, {{ $selectedAddress->kode_pos }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="checkout-address-controls">
                                        <label class="checkout-address-dropdown">
                                            <select name="alamat_id" data-address-select>
                                                @foreach($addresses as $savedAddress)
                                                    <option
                                                        value="{{ $savedAddress->id_alamat }}"
                                                        data-name="{{ $savedAddress->nama_penerima }}"
                                                        data-phone="{{ $savedAddress->no_hp }}"
                                                        data-address="{{ $savedAddress->alamat }}, {{ $savedAddress->kota }}, {{ $savedAddress->kode_pos }}"
                                                        {{ $selectedAddressId === (int) $savedAddress->id_alamat ? 'selected' : '' }}
                                                    >
                                                        {{ $savedAddress->nama_penerima }} - {{ $savedAddress->kota }}{{ $savedAddress->is_default ? ' (Utama)' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>

                                        <button type="button" class="checkout-address-toggle-button" data-address-new-trigger>
                                            <i class="fa-solid fa-plus"></i>
                                            Tambah alamat baru
                                        </button>
                                    </div>

                                    <input type="hidden" name="alamat_mode" value="{{ $addressMode === 'new' ? 'new' : 'existing' }}" data-address-mode-hidden>
                                </div>
                            @else
                                <input type="hidden" name="alamat_mode" value="new" data-address-mode-hidden>
                                <div class="checkout-empty-address">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <div>
                                        <strong>Belum ada alamat tersimpan</strong>
                                        <p>Tambahkan alamat pertamamu di bawah agar checkout bisa diproses.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="checkout-form-divider"></div>

                        <div class="checkout-new-address-panel {{ $addressMode === 'new' || $addresses->isEmpty() ? '' : 'is-hidden' }}" data-new-address-panel>
                            <div class="checkout-new-address-head">
                                <div>
                                    <strong>Tambah alamat baru</strong>
                                    <p>Alamat baru akan otomatis tersimpan dan dijadikan alamat utama setelah checkout berhasil.</p>
                                </div>
                                <button type="button" class="checkout-address-cancel-button {{ $addresses->isEmpty() ? 'is-hidden' : '' }}" data-address-existing-trigger>Pakai alamat tersimpan</button>
                            </div>

                            <div class="checkout-new-address-grid">
                                <label class="checkout-field">
                                    <span>Nama Penerima</span>
                                    <input type="text" name="nama_penerima" value="{{ old('nama_penerima', Auth::user()->nama) }}" {{ $addressMode === 'new' || $addresses->isEmpty() ? 'required' : '' }}>
                                </label>

                                <label class="checkout-field">
                                    <span>Nomor HP</span>
                                    <input type="text" name="no_hp" value="{{ old('no_hp', Auth::user()->no_hp) }}" {{ $addressMode === 'new' || $addresses->isEmpty() ? 'required' : '' }}>
                                </label>

                                <label class="checkout-field checkout-field-wide">
                                    <span>Alamat Lengkap</span>
                                    <textarea name="alamat" rows="4" maxlength="70" {{ $addressMode === 'new' || $addresses->isEmpty() ? 'required' : '' }}>{{ old('alamat') }}</textarea>
                                </label>

                                <label class="checkout-field">
                                    <span>Kota</span>
                                    <input type="text" name="kota" value="{{ old('kota') }}" {{ $addressMode === 'new' || $addresses->isEmpty() ? 'required' : '' }}>
                                </label>

                                <label class="checkout-field">
                                    <span>Kode Pos</span>
                                    <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" {{ $addressMode === 'new' || $addresses->isEmpty() ? 'required' : '' }}>
                                </label>
                            </div>
                        </div>

                    </div>

                <aside class="checkout-summary-card">
                    <div class="checkout-card-head">
                        <h2>Ringkasan Pesanan</h2>
                        <p>{{ $cartItems->count() }} produk siap diproses.</p>
                    </div>

                    <div class="checkout-items">
                        @foreach($cartItems as $item)
                            <div class="checkout-item-row">
                                <img src="{{ $item->produk->gambar_produk_url }}" alt="{{ $item->produk->nama_produk }}">
                                <div class="checkout-item-info">
                                    <h3>{{ $item->produk->nama_produk }}</h3>
                                    <p>{{ $item->produk->kategori?->nama_kategori ?? 'Produk pilihan' }}</p>
                                    <div class="checkout-item-meta">
                                        <span>{{ $item->qty }} {{ strtolower($item->produk->satuan_label) }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }} / {{ strtolower($item->produk->satuan_label) }}</span>
                                        <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="checkout-summary-block">
                        <div class="checkout-summary-block-head">
                            <strong>Subtotal produk</strong>
                            <span>{{ $cartItems->count() }} produk</span>
                        </div>
                        <div class="app-summary-row">
                            <span>Subtotal</span>
                            <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="checkout-delivery-note">
                        <i class="fa-solid fa-truck-fast"></i>
                        <div>
                            <strong>Pengiriman lokal</strong>
                            <p>Biaya kirim saat ini masih gratis untuk area layanan yang tersedia.</p>
                        </div>
                    </div>

                    <div class="checkout-total-box">
                        <div class="app-summary-row">
                            <span>Ongkir</span>
                            <strong>Rp 0</strong>
                        </div>
                        <div class="app-summary-row app-summary-row-total">
                            <span>Total</span>
                            <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="checkout-summary-highlight">
                        <strong>Pesananmu siap dilanjutkan</strong>
                        <p>Lengkapi alamat dan pilih metode pembayaran terbaik sebelum pesanan dibuat.</p>
                    </div>
                </aside>

                    <div class="checkout-form-divider"></div>

                    <div class="checkout-payment-box checkout-field-wide">
                        <div class="checkout-payment-head">
                            <div>
                                <span class="checkout-payment-title">Metode Pembayaran</span>
                                <p>Pilih cara bayar yang paling nyaman untuk pesanan ini.</p>
                            </div>
                        </div>

                        <div class="payment-grid">
                            @foreach($paymentMethods as $method)
                                @php($metode = $method->code)
                                <label class="payment-option">
                                    <input type="radio" name="metode_pembayaran" value="{{ $metode }}" {{ old('metode_pembayaran', $paymentMethods->first()?->code) === $metode ? 'checked' : '' }}>
                                    <span>{{ $method->name }}</span>
                                    <small>
                                        @if($metode === \App\Models\Pesanan::METODE_TRANSFER)
                                            Lanjut ke simulasi pembayaran bank via Midtrans.
                                        @elseif($metode === \App\Models\Pesanan::METODE_E_WALLET)
                                            Lanjut ke simulasi pembayaran digital via Midtrans.
                                        @else
                                            Bayar langsung saat pesanan diterima.
                                        @endif
                                    </small>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-pill checkout-submit">
                        <i class="fa-solid fa-lock"></i>
                        Buat Pesanan
                    </button>
                    </form>
                </section>
            </div>
        </section>
    </section>
</main>

<script>
feather.replace();

document.addEventListener('DOMContentLoaded', () => {
    const selector = document.querySelector('[data-address-selector]');
    if (!selector) {
        return;
    }

    const modeHidden = selector.querySelector('[data-address-mode-hidden]');
    const addressSelect = selector.querySelector('[data-address-select]');
    const newTrigger = selector.querySelector('[data-address-new-trigger]');
    const existingTrigger = document.querySelector('[data-address-existing-trigger]');
    const selectedBox = selector.querySelector('[data-selected-address]');
    const selectedBody = selector.querySelector('[data-selected-address-body]');
    const newAddressPanel = document.querySelector('[data-new-address-panel]');
    const addressInputs = newAddressPanel
        ? newAddressPanel.querySelectorAll('input, textarea')
        : [];

    const syncRequiredState = (showNewForm) => {
        addressInputs.forEach((input) => {
            if (showNewForm) {
                input.setAttribute('required', 'required');
            } else {
                input.removeAttribute('required');
            }
        });
    };

    const showNewAddressForm = () => {
        modeHidden.value = 'new';
        if (selectedBox) {
            selectedBox.classList.add('is-hidden');
        }
        if (newAddressPanel) {
            newAddressPanel.classList.remove('is-hidden');
        }
        if (addressSelect) {
            addressSelect.removeAttribute('required');
        }
        syncRequiredState(true);
    };

    const showSelectedAddress = () => {
        modeHidden.value = 'existing';
        if (newAddressPanel) {
            newAddressPanel.classList.add('is-hidden');
        }
        if (addressSelect) {
            addressSelect.setAttribute('required', 'required');
        }
        syncRequiredState(false);

        if (!selectedBox || !selectedBody || !addressSelect) {
            return;
        }

        const selectedOption = addressSelect.selectedOptions[0];
        selectedBody.innerHTML = selectedOption
            ? `<strong>${selectedOption.dataset.name ?? ''} - ${selectedOption.dataset.phone ?? ''}</strong><p>${selectedOption.dataset.address ?? ''}</p>`
            : '';
        selectedBox.classList.remove('is-hidden');
    };

    if (newTrigger) {
        newTrigger.addEventListener('click', showNewAddressForm);
    }

    if (existingTrigger) {
        existingTrigger.addEventListener('click', showSelectedAddress);
    }

    if (addressSelect) {
        addressSelect.addEventListener('change', showSelectedAddress);
    }

    if (modeHidden.value === 'existing' && addressSelect) {
        showSelectedAddress();
    } else {
        showNewAddressForm();
    }
});
</script>
</body>
</html>
