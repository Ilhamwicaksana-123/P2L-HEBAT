@if(session('success'))
    <div class="action-success-toast" id="actionSuccessToast" role="status" aria-live="polite">
        <div class="action-success-toast-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="action-success-toast-copy">
            <strong>Berhasil</strong>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="action-success-toast-close" data-action-success-toast-close aria-label="Tutup notifikasi">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <script>
    (() => {
        const toast = document.getElementById('actionSuccessToast');

        if (!toast) {
            return;
        }

        const closeToast = () => {
            toast.classList.add('is-hiding');
            window.setTimeout(() => toast.remove(), 220);
        };

        toast.querySelector('[data-action-success-toast-close]')?.addEventListener('click', closeToast);
        window.setTimeout(closeToast, 2600);
    })();
    </script>
@endif
