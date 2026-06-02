<div class="action-modal-overlay" id="actionConfirmModal" aria-hidden="true">
    <div class="action-modal-card" role="dialog" aria-modal="true" aria-labelledby="actionConfirmTitle">
        <button type="button" class="action-modal-close" data-action-close aria-label="Tutup popup konfirmasi">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="action-modal-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <span class="action-modal-kicker" id="actionConfirmKicker">Konfirmasi Aksi</span>
        <h3 id="actionConfirmTitle">Lanjutkan aksi ini?</h3>
        <p id="actionConfirmText">
            Perubahan ini akan diproses setelah kamu menekan tombol konfirmasi.
        </p>

        <div class="action-modal-actions">
            <button type="button" class="action-modal-secondary" data-action-close id="actionConfirmSecondaryButton">Batal</button>
            <button type="button" class="action-modal-primary" id="actionConfirmButton">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('actionConfirmModal');
    const confirmButton = document.getElementById('actionConfirmButton');
    const secondaryButton = document.getElementById('actionConfirmSecondaryButton');
    const actions = modal?.querySelector('.action-modal-actions');
    const title = document.getElementById('actionConfirmTitle');
    const text = document.getElementById('actionConfirmText');
    const kicker = document.getElementById('actionConfirmKicker');

    if (!modal || !confirmButton || !secondaryButton || !actions || !title || !text || !kicker) {
        return;
    }

    let activeForm = null;

    const openModal = (form, config) => {
        activeForm = form;
        title.textContent = config.title || 'Lanjutkan aksi ini?';
        text.textContent = config.text || 'Perubahan ini akan diproses setelah kamu menekan tombol konfirmasi.';
        kicker.textContent = config.kicker || 'Konfirmasi Aksi';
        confirmButton.textContent = config.confirmLabel || 'Ya, Lanjutkan';
        secondaryButton.textContent = config.cancelLabel || 'Batal';
        secondaryButton.hidden = !! config.hideCancel;
        actions.classList.toggle('is-single', !! config.hideCancel);
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('action-modal-open');
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('action-modal-open');
        activeForm = null;
    };

    document.querySelectorAll('[data-action-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();

            const form = document.getElementById(trigger.dataset.actionTarget);

            if (!form) {
                return;
            }

            openModal(form, {
                title: trigger.dataset.actionTitle,
                text: trigger.dataset.actionText,
                kicker: trigger.dataset.actionKicker,
                confirmLabel: trigger.dataset.actionConfirm,
                cancelLabel: trigger.dataset.actionCancel,
                hideCancel: trigger.dataset.actionHideCancel === 'true',
            });
        });
    });

    document.addEventListener('action-modal:open', (event) => {
        const detail = event.detail || {};

        openModal(null, {
            title: detail.title,
            text: detail.text,
            kicker: detail.kicker,
            confirmLabel: detail.confirmLabel,
            cancelLabel: detail.cancelLabel,
            hideCancel: detail.hideCancel,
        });
    });

    modal.querySelectorAll('[data-action-close]').forEach((button) => {
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
            return;
        }

        closeModal();
    });
})();
</script>
