<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite('resources/css/login.css')
</head>

<body class="login-body">

<div class="login-card">

    <!-- LEFT -->
    <div class="login-left">
        
<!-- LOGO -->
<div class="branding">

    <img src="{{ asset('images/logoP2L.png') }}" 
         class="logo animate-pop"
         onerror="this.style.display='none'">

    <h3 class="title animate-fade-up delay-1">P2L</h3>

    <p class="subtitle animate-fade-up delay-2">
        Memenuhi kebutuhan nutrisimu
    </p>

</div>
    </div>

    <!-- RIGHT -->
    <div class="login-right">

        <h2 class="heading">Reset Password</h2>

        @if(session('error'))
            <div class="error-box">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ url('/reset-password') }}">
            @csrf

            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label>Password Baru</label>

                <div class="relative">
                    <input type="password" id="password" name="password"
                        placeholder="Masukkan password baru"
                        class="input" required>

                    <i class="fa-solid fa-eye toggle-icon"
                       id="toggleIcon"
                       onclick="togglePassword()"></i>
                </div>
                @error('password')
                    <span style="color: #dc2626; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>

                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        placeholder="Konfirmasi password baru"
                        class="input" required>

                    <i class="fa-solid fa-eye toggle-icon"
                       onclick="togglePasswordConfirm()"
                       style="cursor: pointer;"></i>
                </div>
            </div>

            <button type="submit" class="btn" id="resetBtn">
                Reset Password
            </button>

        </form>

        <div class="register">
            Kembali ke
            <a href="/login">Login</a>
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

function togglePasswordConfirm() {
    const input = document.getElementById("password_confirmation");
    const icon = event.target;

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

document.querySelector("form").addEventListener("submit", function () {
    const btn = document.getElementById("resetBtn");
    btn.innerHTML = "Loading...";
    btn.disabled = true;
});
</script>

</body>
</html>
