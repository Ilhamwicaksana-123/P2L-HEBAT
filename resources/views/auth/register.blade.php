<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite('resources/css/register.css')
</head>

<body class="register-body">

<div class="register-card">

    <!-- LEFT -->
    <div class="register-left">

        <div class="branding">
            <img src="{{ asset('images/logoP2L.png') }}" 
                 class="logo animate-pop"
                 onerror="this.style.display='none'">

            <h3 class="title animate-fade-up delay-1">P2L</h3>

            <p class="subtitle animate-fade-up delay-2">
                Bergabung dan nikmati produk terbaik
            </p>
        </div>

    </div>

    <!-- RIGHT -->
    <div class="register-right">

        <h2 class="heading">Daftar</h2>

        @if($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}">
            @csrf

            <!-- NAMA -->
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name"
                    placeholder="Masukkan nama lengkap"
                    class="input" value="{{ old('name') }}" required>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                    placeholder="Masukkan email"
                    class="input" value="{{ old('email') }}" required>
            </div>

            <!-- NO HP (BARU) -->
            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="phone"
                    placeholder="08xxxxxxxxxx"
                    class="input"
                    value="{{ old('phone') }}"
                    required>
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label>Password</label>

                <div class="relative">
                    <input type="password" id="password" name="password"
                        placeholder="Masukkan password"
                        class="input" required>

                    <i class="fa-solid fa-eye toggle-icon"
                       id="toggleIcon"
                       onclick="togglePassword()"></i>
                </div>
            </div>

            <button type="submit" class="btn" id="registerBtn">
                Daftar Sekarang
            </button>

        </form>

        <div class="divider">Atau daftar dengan</div>

        <div class="login-link">
            Sudah punya akun?
            <a href="/login">Masuk di sini</a>
        </div>

    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

// Loading button
document.querySelector("form").addEventListener("submit", function () {
    const btn = document.getElementById("registerBtn");
    btn.innerHTML = "Loading...";
    btn.disabled = true;
});

// Auto format No HP (opsional tapi keren)
const phoneInput = document.querySelector('input[name="phone"]');
phoneInput.addEventListener('input', function() {
    let val = this.value.replace(/[^0-9]/g, '');

    if (val.startsWith('62')) {
        val = '0' + val.slice(2);
    }

    this.value = val;
});

// Auto-hide error
const errorBox = document.querySelector('.error-box');
if (errorBox) {
    setTimeout(() => {
        errorBox.style.transition = 'opacity 0.3s ease-out';
        errorBox.style.opacity = '0';
        setTimeout(() => errorBox.remove(), 300);
    }, 3000);
}
</script>

</body>
</html>