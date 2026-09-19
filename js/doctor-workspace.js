(() => {
  const body = document.body;
  const patientModal = document.getElementById('patient-detail-modal');
  const confirmModal = document.getElementById('confirm-modal');
  const completeModal = document.getElementById('complete-modal');

  const setText = (id, value, fallback = 'Not provided') => {
    const element = document.getElementById(id);
    if (element) element.textContent = value && String(value).trim() ? value : fallback;
  };

  const openModal = (modal) => {
    if (!modal) return;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    body.classList.add('modal-open');
    const focusTarget = modal.querySelector('.modal-close, button, [href]');
    if (focusTarget) focusTarget.focus({ preventScroll: true });
  };

  const closeModal = (modal) => {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    if (!document.querySelector('.workspace-modal.is-open')) body.classList.remove('modal-open');
  };

  document.querySelectorAll('.patient-detail-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const data = trigger.dataset;
      setText('modal-patient-name', data.patientName);
      setText('modal-patient-demographic', [data.patientAge ? `${data.patientAge} yrs` : '', data.patientGender ? data.patientGender.toUpperCase() : ''].filter(Boolean).join(' • '));
      setText('modal-patient-phone', data.patientPhone);
      setText('modal-patient-email', data.patientEmail);
      setText('modal-patient-address', data.patientAddress);
      setText('modal-category', data.category);
      setText('modal-day', data.day ? data.day.charAt(0) + data.day.slice(1).toLowerCase() : '');
      setText('modal-time', data.time);
      setText('modal-status', data.status, 'Active');
      setText('modal-appointment-id', data.id ? `Appointment #SC-${data.id}` : '', '');
      openModal(patientModal);
    });
  });

  document.querySelectorAll('[data-close-modal]').forEach((button) => {
    button.addEventListener('click', () => closeModal(patientModal));
  });

  document.querySelectorAll('.confirm-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const appointmentInput = document.getElementById('confirm-appointment-id');
      const copy = document.getElementById('confirm-modal-copy');
      if (appointmentInput) appointmentInput.value = trigger.dataset.id || '';
      if (copy) {
        const patient = trigger.dataset.patient || 'This patient';
        copy.textContent = `${patient}'s request will become a confirmed appointment and can then be completed after consultation.`;
      }
      openModal(confirmModal);
    });
  });

  document.querySelectorAll('[data-close-confirm]').forEach((button) => {
    button.addEventListener('click', () => closeModal(confirmModal));
  });

  const confirmForm = document.getElementById('confirm-form');
  if (confirmForm) {
    confirmForm.addEventListener('submit', () => {
      const button = confirmForm.querySelector('.modal-confirm');
      if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Confirming…';
      }
    });
  }

  document.querySelectorAll('.complete-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const appointmentInput = document.getElementById('complete-appointment-id');
      const copy = document.getElementById('complete-modal-copy');
      if (appointmentInput) appointmentInput.value = trigger.dataset.id || '';
      if (copy) {
        const patient = trigger.dataset.patient || 'this patient';
        copy.textContent = `${patient}'s confirmed appointment will move into the completed appointment archive.`;
      }
      openModal(completeModal);
    });
  });

  document.querySelectorAll('[data-close-complete]').forEach((button) => {
    button.addEventListener('click', () => closeModal(completeModal));
  });

  const completionForm = document.getElementById('complete-form');
  if (completionForm) {
    completionForm.addEventListener('submit', () => {
      const button = completionForm.querySelector('.modal-confirm');
      if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Completing visit…';
      }
    });
  }

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    closeModal(patientModal);
    closeModal(confirmModal);
    closeModal(completeModal);
  });
})();
