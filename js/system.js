(() => {
  'use strict';

  const root = document.documentElement;
  const storageKey = 'smartcare-theme';
  const preferred =
    window.matchMedia &&
    window.matchMedia('(prefers-color-scheme: dark)').matches
      ? 'dark'
      : 'light';

  const saved = localStorage.getItem(storageKey);
  const initialTheme =
    saved === 'dark' || saved === 'light' ? saved : preferred;

  root.dataset.theme = initialTheme;

  const icon = (theme) => (theme === 'dark' ? '☀' : '☾');
  const label = (theme) =>
    theme === 'dark' ? 'Use light theme' : 'Use dark theme';

  function applyTheme(theme) {
    root.dataset.theme = theme;
    localStorage.setItem(storageKey, theme);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.setAttribute('aria-label', label(theme));
      button.setAttribute('title', label(theme));

      const glyph = button.querySelector('[data-theme-icon]');
      if (glyph) glyph.textContent = icon(theme);
    });
  }

  if (!document.querySelector('[data-theme-toggle]')) {
    const button = document.createElement('button');

    button.type = 'button';
    button.className = 'theme-toggle theme-toggle--floating';
    button.setAttribute('data-theme-toggle', '');

    button.innerHTML =
      '<span data-theme-icon aria-hidden="true"></span>' +
      '<span class="theme-toggle__label">Theme</span>';

    document.body.appendChild(button);
  }

  document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () =>
      applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark')
    );
  });

  applyTheme(initialTheme);

  const revealTargets = document.querySelectorAll(
    '.stat-card, .service-card, .doctor-card, .role-card, .appointment-card, .admin-stat-card, .provider-card, .patient-card'
  );

  if ('IntersectionObserver' in window && revealTargets.length) {
    revealTargets.forEach((element) =>
      element.classList.add('sc-reveal')
    );

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08 }
    );

    revealTargets.forEach((element) =>
      observer.observe(element)
    );
  }

  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (event.defaultPrevented) return;

      const submit =
        event.submitter ||
        form.querySelector(
          'button[type="submit"], input[type="submit"]'
        );

      if (!submit || submit.dataset.keepEnabled === 'true') return;

      window.setTimeout(() => {
        submit.disabled = true;
        submit.classList.add('is-submitting');
      }, 0);
    });
  });

  document
    .querySelectorAll(
      '.stat-card strong, .admin-stat-card strong, .doctor-stat strong'
    )
    .forEach((element) => {
      const raw = element.textContent.trim();

      if (!/^\d+$/.test(raw)) return;

      const target = Number(raw);

      if (target <= 0 || target > 99999) return;

      const started = performance.now();
      const duration = 520;

      const tick = (now) => {
        const progress = Math.min(
          1,
          (now - started) / duration
        );

        element.textContent = String(
          Math.round(
            target *
              (1 - Math.pow(1 - progress, 3))
          )
        );

        if (progress < 1) {
          requestAnimationFrame(tick);
        }
      };

      requestAnimationFrame(tick);
    });
})();