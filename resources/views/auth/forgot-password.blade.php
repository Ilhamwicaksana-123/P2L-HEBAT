<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>

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

        <h2 class="heading">Lupa Password</h2>

        <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 20px;">
            Masukkan email Anda dan kami akan mengirimkan link untuk reset password
        </p>

        @if(session('success'))
            <div class="error-box" style="background-color: #dcfce7; color: #166534;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="error-box">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ url('/forgot-password') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                    placeholder="Masukkan email Anda"
                    class="input" value="{{ old('email') }}" required>
                @error('email')
                    <span style="color: #dc2626; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn" id="resetBtn">
                Kirim Link Reset
            </button>

        </form>

        <div class="register">
            Ingat password Anda?
            <a href="/login">Kembali ke Login</a>
        </div>

    </div>

</div>

<script>
document.querySelector("form").addEventListener("submit", function () {
    const btn = document.getElementById("resetBtn");
    btn.innerHTML = "Loading...";
    btn.disabled = true;
});
</script>

</body>
</html>
