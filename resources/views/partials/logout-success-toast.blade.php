@if(session('logout_success'))
    <div class="logout-toast" id="logoutSuccessToast" role="status" aria-live="polite">
        <div class="logout-toast-icon">
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>
        <div class="logout-toast-copy">
            <strong>Berhasil keluar</strong>
            <span>{{ session('logout_success') }}</span>
        </div>
        <button type="button" class="logout-toast-close" data-logout-toast-close aria-label="Tutup notifikasi">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <script>
    (() => {
        const toast = document.getElementById('logoutSuccessToast');

        if (!toast) {
            return;
        }

        const closeToast = () => {
            toast.classList.add('is-hiding');
            window.setTimeout(() => toast.remove(), 220);
        };

        toast.querySelector('[data-logout-toast-close]')?.addEventListener('click', closeToast);
        window.setTimeout(closeToast, 2600);
    })();
    </script>
@endif
