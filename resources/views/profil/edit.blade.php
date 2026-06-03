<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/profil-edit.css'])
</head>

<body class="app-page-body profil-edit-body">
<header class="profil-edit-navbar">
    <div class="profil-edit-nav-inner">
        <div class="profil-edit-brand">
            <img src="{{ asset('images/logo-putih.png') }}" class="profil-edit-logo" alt="P2L Hebat">
            <span>P2L Hebat</span>
        </div>
        <a href="{{ $backRoute }}" class="profil-edit-back">{{ $backLabel }}</a>
    </div>
</header>

<main class="profil-edit-wrapper fade-in">
    <section class="profil-edit-hero app-fade-up">
        <div>
            <span class="profil-edit-kicker">Edit Profil</span>
            <h1>Hai, {{ $user->nama }}</h1>
            <p>Perbarui data akunmu agar profil tetap lengkap, rapi, dan mudah dikenali.</p>
        </div>
    </section>

    @if($errors->any())
        <div class="error-box text-center">
            @foreach($errors->all() as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    <div class="profil-edit-stack">
        <div class="profil-edit-card app-fade-up app-delay-1">
            <div class="profil-edit-grid">
                <div class="profil-edit-avatar-panel">
                    <div class="avatar-box mx-auto">
                        <img
                            id="profilePhotoPreview"
                            src="{{ $user->photo_url }}"
                            alt="{{ $user->nama }}"
                            data-default-src="{{ $user->photo_url }}"
                        >
                    </div>

                    <h2>{{ $user->nama }}</h2>
                    <p>{{ $user->email }}</p>

                    <label for="photoInput" class="photo-label">Upload Foto</label>
                    <input id="photoInput" type="file" name="photo" form="formProfile" hidden>

                    <p id="photoFileName" class="photo-file-name">Belum ada foto baru dipilih.</p>
                    <p class="photo-hint">Format JPG, PNG, GIF. Maksimal 2MB.</p>
                </div>

                <form id="formProfile" method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="profil-edit-form-grid">
                    @csrf

                    <div class="form-section-head">
                        <div>
                            <span class="section-chip">Profil</span>
                            <h2>Informasi Akun</h2>
                        </div>
                        <p>Perbarui data utama akun dan foto profil pada bagian ini.</p>
                    </div>

                    <div>
                        <label class="input-label">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" class="input-field">
                    </div>

                    <div>
                        <label class="input-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" maxlength="30">
                    </div>

                    <div>
                        <label class="input-label">Nomor Handphone</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="input-field">
                    </div>

                    <div class="profil-edit-actions">
                        <a href="{{ $backRoute }}" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-save">Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="profil-edit-card password-card app-fade-up app-delay-2">
            <form method="POST" action="{{ route('profil.password.update') }}" class="profil-edit-form-grid">
                @csrf

                <div class="form-section-head security-head">
                    <div>
                        <span class="section-chip">Keamanan</span>
                        <h2>Ubah Password</h2>
                    </div>
                    <p>
                        @if($user->google_id && blank($user->password))
                            Karena kamu masuk dengan Google, buat password manual di sini agar akun juga bisa login tanpa Google.
                        @else
                            Demi keamanan, masukkan password saat ini terlebih dahulu sebelum mengganti password.
                        @endif
                    </p>
                </div>

                <div class="security-banner">
                    <div class="security-banner-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h3>Jaga akses akunmu tetap aman</h3>
                        <p>Password baru akan digunakan untuk login manual, sementara akses Google tetap bisa dipakai seperti biasa.</p>
                    </div>
                </div>

                <div class="password-box">
                    <div class="password-field-group">
                        <label class="input-label">Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="Masukkan password saat ini" class="input-field">
                    </div>

                    <div class="password-field-group">
                        <label class="input-label">Password Baru</label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" class="input-field">
                    </div>

                    <div class="password-field-group">
                        <label class="input-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="input-field">
                    </div>
                </div>

                <div class="security-checklist">
                    <div class="security-check-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Gunakan minimal 8 karakter.</span>
                    </div>
                    <div class="security-check-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Gabungkan huruf dan angka agar lebih kuat.</span>
                    </div>
                    <div class="security-check-item">
                        <i class="fa-solid fa-check"></i>
                        <span>Jangan gunakan password yang sama dengan akun lain.</span>
                    </div>
                </div>

                <div class="profil-edit-actions">
                    <a href="{{ $backRoute }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-save">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
const profilePhotoInput = document.getElementById('photoInput');
const profilePhotoPreview = document.getElementById('profilePhotoPreview');
const photoFileName = document.getElementById('photoFileName');

if (profilePhotoInput && profilePhotoPreview && photoFileName) {
    profilePhotoInput.addEventListener('change', (event) => {
        const [file] = event.target.files || [];

        if (!file) {
            profilePhotoPreview.src = profilePhotoPreview.dataset.defaultSrc;
            photoFileName.textContent = 'Belum ada foto baru dipilih.';
            return;
        }

        profilePhotoPreview.src = URL.createObjectURL(file);
        photoFileName.textContent = `Preview siap disimpan: ${file.name}`;
    });
}
</script>
</body>
</html>
