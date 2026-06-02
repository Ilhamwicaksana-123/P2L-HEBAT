<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>
<body class="app-page-body" style="margin:0; min-height:100vh; font-family:'Poppins',sans-serif; color:#14532d;">
@php
    $user = auth()->user();
    $isAdminUser = $user && in_array($user->role, ['super_admin', 'admin'], true);
    $homeRoute = $isAdminUser ? route('admin.dashboard') : route('beranda');
    $homeLabel = $isAdminUser ? 'Kembali ke Dashboard Admin' : 'Kembali ke Beranda';
    $message = $exception?->getMessage() ?: 'Kamu tidak memiliki izin untuk membuka halaman ini.';
@endphp

<main style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:32px;">
    <section style="width:min(100%, 760px); background:rgba(255,255,255,0.88); backdrop-filter:blur(12px); border:1px solid rgba(34,197,94,0.18); border-radius:28px; box-shadow:0 24px 60px rgba(20,83,45,0.12); padding:40px 32px;">
        <div style="display:inline-flex; align-items:center; gap:10px; padding:10px 16px; border-radius:999px; background:#dcfce7; color:#166534; font-weight:600; font-size:14px;">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Akses Ditolak</span>
        </div>

        <div style="margin-top:20px;">
            <p style="margin:0; font-size:14px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:#16a34a;">Error 403</p>
            <h1 style="margin:12px 0 14px; font-family:'Playfair Display',serif; font-size:clamp(36px, 7vw, 56px); line-height:1.05; color:#14532d;">
                Halaman ini bukan untuk role yang sedang aktif.
            </h1>
            <p style="margin:0; max-width:640px; font-size:16px; line-height:1.8; color:#166534;">
                {{ $message }}
                @if($isAdminUser)
                    Area belanja pengguna dibatasi agar admin tetap fokus pada pengelolaan dashboard, produk, kategori, pesanan, dan pengguna.
                @else
                    Coba kembali ke halaman utama atau login dengan akun yang memiliki akses yang sesuai.
                @endif
            </p>
        </div>

        <div style="margin-top:28px; display:flex; flex-wrap:wrap; gap:14px;">
            <a href="{{ $homeRoute }}" style="display:inline-flex; align-items:center; gap:10px; padding:14px 22px; border-radius:999px; text-decoration:none; background:linear-gradient(135deg, #16a34a, #84cc16); color:white; font-weight:600; box-shadow:0 14px 28px rgba(22,163,74,0.25);">
                <i class="fa-solid fa-arrow-left"></i>
                <span>{{ $homeLabel }}</span>
            </a>

            <a href="{{ route('produk.index') }}" style="display:inline-flex; align-items:center; gap:10px; padding:14px 22px; border-radius:999px; text-decoration:none; background:white; color:#166534; font-weight:600; border:1px solid rgba(34,197,94,0.22);">
                <i class="fa-solid fa-store"></i>
                <span>Lihat Produk</span>
            </a>

            @if($user)
                <a href="{{ route('profil.show') }}" style="display:inline-flex; align-items:center; gap:10px; padding:14px 22px; border-radius:999px; text-decoration:none; background:white; color:#166534; font-weight:600; border:1px solid rgba(34,197,94,0.22);">
                    <i class="fa-solid fa-user"></i>
                    <span>Buka Profil</span>
                </a>
            @endif
        </div>
    </section>
</main>
</body>
</html>
