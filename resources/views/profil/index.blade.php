<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/profil.css'])
</head>

<body class="app-page-body profil-page-body">
@include('partials.action-success-toast')
<header class="profil-navbar">
    <div class="profil-nav-inner">
        <a href="{{ route('beranda') }}" class="profil-brand">
            <img src="{{ asset('images/logo-putih.png') }}" class="logo-nav" alt="P2L Hebat">
            <span>P2L Hebat</span>
        </a>

        <div class="profil-nav-actions">
            <span class="profil-greeting">Hai, {{ $user->nama }}</span>
            <a href="{{ $backRoute }}" class="profil-back-link">{{ $backLabel }}</a>
        </div>
    </div>
</header>

<main class="profil-wrapper fade-in">
    <section class="profil-hero-card app-fade-up">
        <div class="profil-hero-copy">
            <span class="profil-kicker">Profil Saya</span>
            <h1>Hai, {{ $user->nama }}</h1>
            <p>
                Kelola informasi akunmu, Pastikan profilmu selalu lengkap dan terbaru agar kami bisa memberikan layanan terbaik untukmu.
            </p>
        </div>

        <div class="profil-hero-avatar">
            <div class="avatar-box large-avatar">
                <img src="{{ $user->photo_url }}" alt="{{ $user->nama }}">
            </div>
        </div>
    </section>

    <section class="profil-grid">
        <div class="profil-main-card app-fade-up app-delay-1">
            <div class="profil-section-head">
                <div>
                <span class="section-chip">Akun</span>
                    <h2>Informasi Utama</h2>
                </div>
                <span class="label-chip">{{ $user->google_id ? 'Google Account' : 'Manual Account' }}</span>
            </div>

            <div class="detail-grid">
                <div class="detail-panel">
                    <p>Email</p>
                    <strong>{{ $user->email }}</strong>
                </div>

                <div class="detail-panel">
                    <p>No HP</p>
                    <strong>{{ $user->no_hp ?? '-' }}</strong>
                </div>

                <div class="detail-panel">
                    <p>Terdaftar</p>
                    <strong>{{ optional($user->created_at)->format('d M Y') }}</strong>
                </div>

                <div class="detail-panel detail-panel-wide">
                    <p>Status akun</p>
                    <strong>{{ $user->google_id ? 'Terhubung dengan Google' : 'Akun manual aktif' }}</strong>
                </div>
            </div>
        </div>

        <aside class="profil-side-card app-fade-up app-delay-2">
            <div class="profil-side-head">
                <h3>Kelola akun</h3>
                <p>
                    Perbarui informasi akun, ubah foto profil, atau keluar dari sesi saat ini. Semua tindakan penting untuk menjaga keamanan dan kenyamanan penggunaan akunmu.
                </p>
            </div>

            <div class="profil-action-card">


                <div class="profil-actions">
                    <a href="{{ route('profil.edit') }}" class="btn-brand">Edit Profil</a>

                    <form action="{{ route('logout') }}" method="POST" id="profil-logout-form">
                        @csrf
                        <button type="button" class="btn-ghost-red" data-logout-trigger data-logout-target="profil-logout-form">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
    </section>

    <section class="profil-address-section app-fade-up app-delay-2" id="alamat-section">
        <div class="profil-address-head">
            <div>
                <span class="section-chip section-chip-soft">Alamat</span>
                <h2>Daftar Alamat</h2>
            </div>
        </div>

        <div class="profil-address-layout">
            <div class="profil-address-card profil-address-card-list">
                <div class="profil-address-card-head">
                    <h3>Alamat Tersimpan</h3>
                    <p>{{ $addresses->count() }} alamat tersedia di akunmu.</p>
                </div>

                <div class="profil-address-list">
                    @forelse($addresses as $address)
                        <article class="profil-address-item">
                            <div class="profil-address-item-top">
                                <div class="profil-address-item-person">
                                    <div class="profil-address-item-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $address->nama_penerima }}</strong>
                                        <p>{{ $address->no_hp }}</p>
                                    </div>
                                </div>

                                <details class="profil-address-edit-box">
                                    <summary>Edit alamat</summary>

                                    <form action="{{ route('alamat.update', $address->id_alamat) }}" method="POST" class="profil-address-form profil-address-form-edit">
                                        @csrf
                                        @method('PATCH')

                                        <label class="profil-address-field">
                                            <span>Nama Penerima</span>
                                            <input type="text" name="nama_penerima" value="{{ $address->nama_penerima }}" required>
                                        </label>

                                        <label class="profil-address-field">
                                            <span>Nomor HP</span>
                                            <input type="text" name="no_hp" value="{{ $address->no_hp }}" required>
                                        </label>

                                        <label class="profil-address-field profil-address-field-wide">
                                            <span>Alamat Lengkap</span>
                                            <textarea name="alamat" rows="3" maxlength="70" required>{{ $address->alamat }}</textarea>
                                        </label>

                                        <label class="profil-address-field">
                                            <span>Kota</span>
                                            <input type="text" name="kota" value="{{ $address->kota }}" required>
                                        </label>

                                        <label class="profil-address-field">
                                            <span>Kode Pos</span>
                                            <input type="text" name="kode_pos" value="{{ $address->kode_pos }}" required>
                                        </label>

                                        <label class="profil-address-checkbox profil-address-field-wide">
                                            <input type="checkbox" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }}>
                                            <span>Jadikan alamat utama</span>
                                        </label>

                                        <button type="submit" class="btn-brand">Simpan Perubahan</button>
                                    </form>
                                </details>
                            </div>
                            <div class="profil-address-item-body">
                                <p>{{ $address->alamat }}</p>
                                <p>{{ $address->kota }}, {{ $address->kode_pos }}</p>
                            </div>

                            <div class="profil-address-actions">
                                @if($address->is_default)
                                    <span class="profil-address-default-badge">Alamat utama</span>
                                @endif

                                <form action="{{ route('alamat.destroy', $address->id_alamat) }}" method="POST" id="delete-address-{{ $address->id_alamat }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        class="btn-ghost-red profil-address-delete"
                                        data-action-trigger
                                        data-action-target="delete-address-{{ $address->id_alamat }}"
                                        data-action-kicker="Hapus Alamat"
                                        data-action-title="Hapus alamat ini?"
                                        data-action-text="Alamat akan dihapus dari daftar alamatmu."
                                        data-action-confirm="Ya, Hapus"
                                    >
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="profil-address-empty">
                            <i class="fa-solid fa-location-dot"></i>
                            <div>
                                <strong>Belum ada alamat</strong>
                                <p>Tambahkan alamat pertamamu lewat form di samping agar checkout bisa lebih cepat.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="profil-address-card profil-address-card-form">
                <div class="profil-address-card-head">
                    <h3>Tambah Alamat Baru</h3>
                    <p>Alamat yang ditambahkan di sini akan langsung tersimpan sebagai alamat utama.</p>
                </div>

                <form action="{{ route('alamat.store') }}" method="POST" class="profil-address-form">
                    @csrf

                    <label class="profil-address-field">
                        <span>Nama Penerima</span>
                        <input type="text" name="nama_penerima" value="{{ old('nama_penerima', $user->nama) }}" required>
                    </label>

                    <label class="profil-address-field">
                        <span>Nomor HP</span>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" required>
                    </label>

                    <label class="profil-address-field profil-address-field-wide">
                        <span>Alamat Lengkap</span>
                        <textarea name="alamat" rows="4" maxlength="70" required>{{ old('alamat') }}</textarea>
                    </label>

                    <label class="profil-address-field">
                        <span>Kota</span>
                        <input type="text" name="kota" value="{{ old('kota') }}" required>
                    </label>

                    <label class="profil-address-field">
                        <span>Kode Pos</span>
                        <input type="text" name="kode_pos" value="{{ old('kode_pos') }}" required>
                    </label>

                    <button type="submit" class="btn-brand">Simpan Alamat</button>
                </form>
            </div>
        </div>
    </section>
</main>
@include('partials.logout-modal')
@include('partials.action-confirm-modal')
</body>
</html>
