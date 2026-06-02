<div class="logout-modal-overlay" id="logoutModal" aria-hidden="true">
    <div class="logout-modal-card" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
        <button type="button" class="logout-modal-close" data-logout-close aria-label="Tutup popup logout">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="logout-modal-icon">
            <i class="fa-solid fa-right-from-bracket"></i>
        </div>

        <span class="logout-modal-kicker">Logout</span>
        <h3 id="logoutModalTitle">Anda yakin ingin keluar?</h3>
        <p>
            Kamu akan keluar dari sesi saat ini dan perlu login kembali untuk mengakses akunmu.
        </p>

        <div class="logout-modal-actions">
            <button type="button" class="logout-modal-secondary" data-logout-close>Batal</button>
            <button type="button" class="logout-modal-primary" id="logoutModalConfirm">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('logoutModal');
    const confirmButton = document.getElementById('logoutModalConfirm');

    if (!modal || !confirmButton) {
        return;
    }

    let activeForm = null;

    const openModal = (form) => {
        activeForm = form;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('logout-modal-open');
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('logout-modal-open');
        activeForm = null;
    };

    document.querySelectorAll('[data-logout-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();

            const targetId = trigger.dataset.logoutTarget;
            const form = document.getElementById(targetId);

            if (form) {
                openModal(form);
            }
        });
    });

    modal.querySelectorAll('[data-logout-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    confirmButton.addEventListener('click', () => {
        if (activeForm) {
            activeForm.submit();
        }
    });
})();
</script>
