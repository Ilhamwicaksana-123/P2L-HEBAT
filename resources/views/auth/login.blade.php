<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite('resources/css/login.css')
</head>

<body class="login-body">
<div class="login-card">
    <div class="login-left">
        <div class="branding">
            <img src="{{ asset('images/logoP2L.png') }}"
                 class="logo logo-animate"
                 onerror="this.style.display='none'">

            <h3 class="title animate-fade-up delay-1">P2L</h3>

            <p class="subtitle animate-fade-up delay-2">
                Siap memenuhi kebutuhan nutrisimu
            </p>
        </div>
    </div>

    <div class="login-right">
        <div class="login-right animate-fade-up"></div>

        <h2 class="heading">Login</h2>

        @if(session('error'))
            <div class="error-box">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="Masukkan email"
                       class="input"
                       required>
            </div>

            <div class="form-group">
                <label>Password</label>

                <div class="relative">
                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="Masukkan password"
                           class="input"
                           required>

                    <i class="fa-solid fa-eye toggle-icon"
                       id="toggleIcon"
                       onclick="togglePassword()"></i>
                </div>
            </div>

            <div style="text-align: right; margin-bottom: 15px;">
                <a href="{{ route('password.request') }}" style="color: #22c55e; font-size: 13px; text-decoration: none;">
                    Lupa Password?
                </a>
            </div>

            <button type="submit" class="btn" id="loginBtn">
                Lanjutkan
            </button>
        </form>

        <div class="divider">Atau masuk dengan</div>

        <div class="google-btn">
            <a href="{{ route('google.login', array_filter([
                'intended_action' => request()->query('intended_action'),
                'produk_id' => request()->query('produk_id'),
                'qty' => request()->query('qty'),
            ])) }}" style="display: flex; align-items: center; gap: 8px; text-decoration: none; color: inherit;">
                <img src="{{ asset('images/google.png') }}">
                Masuk dengan Google
            </a>
        </div>

        <div class="register">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar Sekarang</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.querySelector('form').addEventListener('submit', function () {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = 'Loading...';
    btn.disabled = true;
});

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
