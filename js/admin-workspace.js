(function () {
    'use strict';

    document.querySelectorAll('[data-admin-search]').forEach(function (input) {
        var targetId = input.getAttribute('data-admin-search');
        var target = document.getElementById(targetId);
        if (!target) return;

        var emptyState = document.getElementById(targetId + '-empty');
        var counter = document.getElementById(targetId.replace('-directory', '') + '-result-count');
        var cards = Array.prototype.slice.call(target.querySelectorAll('[data-search-text]'));

        function filterRecords() {
            var query = input.value.trim().toLowerCase();
            var visible = 0;

            cards.forEach(function (card) {
                var matches = !query || (card.getAttribute('data-search-text') || '').indexOf(query) !== -1;
                card.hidden = !matches;
                if (matches) visible += 1;
            });

            if (counter) counter.textContent = visible;
            if (emptyState) emptyState.hidden = visible !== 0;
        }

        input.addEventListener('input', filterRecords);
    });

    var modal = document.getElementById('admin-confirm-modal');
    if (!modal) return;

    var title = document.getElementById('admin-modal-title');
    var message = document.getElementById('admin-modal-message');
    var confirmButton = document.getElementById('admin-modal-confirm');
    var modalIcon = document.getElementById('admin-modal-icon');
    var pendingForm = null;
    var pendingButton = null;

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('admin-modal-open');
        pendingForm = null;
        pendingButton = null;
    }

    function openModal(form, button) {
        pendingForm = form;
        pendingButton = button;
        title.textContent = button.getAttribute('data-confirm-title') || 'Confirm this action?';
        message.textContent = button.getAttribute('data-confirm-message') || 'Please confirm before SmartCare updates this record.';
        confirmButton.textContent = button.getAttribute('data-confirm-label') || 'Confirm';
        var tone = button.getAttribute('data-confirm-tone') || 'danger';
        confirmButton.className = 'modal-primary tone-' + tone;
        modalIcon.className = 'admin-modal-icon tone-' + tone;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('admin-modal-open');
        confirmButton.focus();
    }

    document.querySelectorAll('.js-confirm-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === 'true') return;
            event.preventDefault();
            var button = event.submitter || form.querySelector('[data-confirm-title]');
            if (button) openModal(form, button);
        });
    });

    document.querySelectorAll('.js-confirm-form-group').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.confirmed === 'true') return;
            event.preventDefault();
            var button = event.submitter;
            if (button) openModal(form, button);
        });
    });

    confirmButton.addEventListener('click', function () {
        if (!pendingForm || !pendingButton) return;
        pendingForm.dataset.confirmed = 'true';

        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = pendingButton.name;
        hidden.value = pendingButton.value || '1';
        pendingForm.appendChild(hidden);

        confirmButton.disabled = true;
        confirmButton.textContent = 'Updating...';
        pendingForm.submit();
    });

    modal.querySelectorAll('[data-modal-close]').forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('open')) closeModal();
    });
})();
