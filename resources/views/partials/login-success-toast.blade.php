@if(session('login_success'))
    <div class="login-toast" id="loginSuccessToast" role="status" aria-live="polite">
        <div class="login-toast-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="login-toast-copy">
            <strong>Berhasil masuk</strong>
            <span>{{ session('login_success') }}</span>
        </div>
        <button type="button" class="login-toast-close" data-login-toast-close aria-label="Tutup notifikasi">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <script>
    (() => {
        const toast = document.getElementById('loginSuccessToast');

        if (!toast) {
            return;
        }

        const closeToast = () => {
            toast.classList.add('is-hiding');
            window.setTimeout(() => toast.remove(), 220);
        };

        toast.querySelector('[data-login-toast-close]')?.addEventListener('click', closeToast);
        window.setTimeout(closeToast, 2600);
    })();
    </script>
@endif
