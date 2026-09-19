const menuButton = document.querySelector('.nav__toggle');
const menu = document.querySelector('.nav__menu');

if (menuButton && menu) {
  const setMenuState = (open) => {
    menuButton.classList.toggle('is-open', open);
    menu.classList.toggle('is-open', open);
    menuButton.setAttribute('aria-expanded', String(open));
    menuButton.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
    document.body.classList.toggle('menu-open', open);
  };

  menuButton.addEventListener('click', () => setMenuState(!menu.classList.contains('is-open')));

  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => setMenuState(false));
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 1000) setMenuState(false);
  });
}

const doctorSearch = document.getElementById('doctor-search');
const doctorSpeciality = document.getElementById('doctor-speciality');
const doctorCards = Array.from(document.querySelectorAll('[data-doctor-card]'));
const doctorEmpty = document.getElementById('doctor-filter-empty');

function filterDoctors() {
  if (!doctorCards.length) return;
  const query = (doctorSearch?.value || '').trim().toLowerCase();
  const speciality = doctorSpeciality?.value || '';
  let visible = 0;
  doctorCards.forEach((card) => {
    const matchesText = !query || (card.dataset.search || '').includes(query);
    const matchesSpeciality = !speciality || card.dataset.speciality === speciality;
    const show = matchesText && matchesSpeciality;
    card.hidden = !show;
    if (show) visible += 1;
  });
  if (doctorEmpty) doctorEmpty.hidden = visible !== 0;
}

doctorSearch?.addEventListener('input', filterDoctors);
doctorSpeciality?.addEventListener('change', filterDoctors);
