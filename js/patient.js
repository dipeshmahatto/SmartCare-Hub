(function () {
    const modal = document.getElementById('cancel-modal');
    if (!modal) return;

    const idInput = document.getElementById('cancel-appointment-id');
    const message = document.getElementById('cancel-message');
    const openButtons = document.querySelectorAll('.js-cancel-appointment');
    const closeButtons = modal.querySelectorAll('[data-modal-close]');

    function openModal(button) {
        idInput.value = button.dataset.appointmentId || '';
        const doctor = button.dataset.doctor || 'your doctor';
        message.textContent = `Your appointment with ${doctor} will be marked cancelled and kept in your history.`;
        modal.hidden = false;
        document.body.classList.add('modal-open');
        const close = modal.querySelector('[data-modal-close]');
        if (close) close.focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('modal-open');
        idInput.value = '';
    }

    openButtons.forEach((button) => button.addEventListener('click', () => openModal(button)));
    closeButtons.forEach((button) => button.addEventListener('click', closeModal));

    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) closeModal();
    });
})();
