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

    @unless(in_array($user->role, ['super_admin', 'admin'], true))
        @include('alamat.alamat')
    @endunless
</main>
@include('partials.logout-modal')
@include('partials.action-confirm-modal')
</body>
</html>
