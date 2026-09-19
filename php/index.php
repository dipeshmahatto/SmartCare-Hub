<?php
include "database.php";

$doctors = [];
$sql = "SELECT id, fullName, speciality, qualification, address, gender FROM doctor ORDER BY id DESC LIMIT 6";
$result = mysqli_query($conn, $sql);

if ($result) {
  while ($row = mysqli_fetch_assoc($result)) {
    $doctors[] = $row;
  }
}

$specialityFilters = array_values(array_unique(array_filter(array_column($doctors, 'speciality'))));
sort($specialityFilters, SORT_NATURAL | SORT_FLAG_CASE);

function e($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="SmartCare Hub makes it simple to discover trusted doctors, book appointments, and manage healthcare in one place." />
  <link rel="stylesheet" href="../css/front.css" />
  <link rel="stylesheet" href="../css/footer.css" />
  <title>SmartCare Hub | Smarter Healthcare, Simpler Appointments</title>
</head>

<body>
  <header class="hero" id="home">
    <nav class="nav section__container" aria-label="Primary navigation">
      <a class="nav__logo" href="index.php" aria-label="SmartCare Hub home">
        <span class="nav__logo-mark" aria-hidden="true">+</span>
        <span>SmartCare<span>Hub</span></span>
      </a>

      <button class="nav__toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="primary-menu">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="nav__menu" id="primary-menu">
        <ul class="nav__links">
          <li><a href="#home">Home</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#doctors">Doctors</a></li>
          <li><a href="#aboutus">About</a></li>
        </ul>
        <div class="nav__actions">
          <button class="theme-toggle" type="button" data-theme-toggle><span data-theme-icon aria-hidden="true"></span><span class="theme-toggle__label">Theme</span></button>
          <a class="nav__login" href="patient/patient_login.php">Patient Login</a>
          <a class="btn btn--small" href="patient/patient_registration.php">Get Started</a>
        </div>
      </div>
    </nav>

    <div class="section__container hero__container">
      <div class="hero__content">
        <div class="eyebrow"><span></span>Healthcare made simpler</div>
        <h1>Your health deserves <span>smart, connected care.</span></h1>
        <p class="hero__lead">
          Find the right doctor, book appointments faster, and keep your healthcare journey organized—all from one simple platform.
        </p>
        <div class="hero__actions">
          <a class="btn" href="patient/patient_login.php">Book an Appointment</a>
          <a class="btn btn--ghost" href="#doctors">Explore Doctors</a>
        </div>
        <div class="hero__trust">
          <div class="avatar-stack" aria-hidden="true">
            <span>SC</span><span>DR</span><span>+</span>
          </div>
          <div><strong>Simple. Secure. Human.</strong><small>Designed around patients and healthcare teams.</small></div>
        </div>
      </div>

      <div class="hero__visual" aria-label="SmartCare appointment overview">
        <div class="hero__glow hero__glow--one"></div>
        <div class="hero__glow hero__glow--two"></div>
        <div class="hero__image-card">
          <img src="../img/doctor-1.jpg" alt="Healthcare professional at SmartCare Hub" />
          <div class="availability"><span></span>Doctors available</div>
        </div>
        <div class="floating-card floating-card--appointment">
          <div class="floating-card__icon">✓</div>
          <div><small>Appointment</small><strong>Booking made easy</strong></div>
        </div>
        <div class="floating-card floating-card--trust">
          <strong>24/7</strong>
          <span>Access to your care portal</span>
        </div>
      </div>
    </div>

    <div class="section__container stats" aria-label="SmartCare highlights">
      <div><strong>3</strong><span>Dedicated user roles</span></div>
      <div><strong><?= count($doctors) > 0 ? e(count($doctors)) . '+' : '6+' ?></strong><span>Doctors ready to discover</span></div>
      <div><strong>1</strong><span>Connected care platform</span></div>
      <div><strong>Fast</strong><span>Appointment workflow</span></div>
    </div>
  </header>

  <main>
    <section class="section__container section" id="services">
      <div class="section__intro section__intro--center">
        <div class="eyebrow eyebrow--dark"><span></span>Built around your journey</div>
        <h2 class="section__header">Healthcare without the unnecessary complexity</h2>
        <p>SmartCare Hub connects patients, doctors, and administrators through one focused appointment management experience.</p>
      </div>

      <div class="services__grid">
        <article class="service-card">
          <div class="service-card__icon">01</div>
          <h3>Find the right doctor</h3>
          <p>Explore approved doctors by specialty and choose the care that fits your needs.</p>
          <a href="#doctors">Browse doctors <span>→</span></a>
        </article>
        <article class="service-card service-card--featured">
          <div class="service-card__icon">02</div>
          <h3>Book with confidence</h3>
          <p>Sign in, choose your doctor and schedule, then track appointment progress from your portal.</p>
          <a href="patient/patient_login.php">Book appointment <span>→</span></a>
        </article>
        <article class="service-card">
          <div class="service-card__icon">03</div>
          <h3>Stay organized</h3>
          <p>Patients, doctors, and admins each get a dedicated workspace for their healthcare tasks.</p>
          <a href="#roles">Explore roles <span>→</span></a>
        </article>
      </div>
    </section>

    <section class="doctors section" id="doctors">
      <div class="section__container">
        <div class="doctors__header">
          <div class="section__intro">
            <div class="eyebrow eyebrow--dark"><span></span>Trusted professionals</div>
            <h2 class="section__header">Meet doctors on SmartCare</h2>
            <p>Discover healthcare professionals already available through the SmartCare Hub platform.</p>
          </div>
          <a class="text-link" href="patient/patient_login.php">View through patient portal <span>→</span></a>
        </div>

        <div class="doctor-filters" aria-label="Doctor filters">
          <label class="doctor-search"><span>Search</span><input type="search" id="doctor-search" placeholder="Doctor or speciality" autocomplete="off"></label>
          <label class="doctor-speciality-filter"><span>Speciality</span><select id="doctor-speciality"><option value="">All specialities</option><?php foreach ($specialityFilters as $speciality): ?><option value="<?= e($speciality) ?>"><?= e($speciality) ?></option><?php endforeach; ?></select></label>
        </div>

        <div class="doctors__grid">
          <?php if (!empty($doctors)): ?>
            <?php foreach ($doctors as $index => $doctor): ?>
              <article class="doctor-card" data-doctor-card data-search="<?= e(strtolower($doctor['fullName'] . ' ' . $doctor['speciality'] . ' ' . $doctor['qualification'])) ?>" data-speciality="<?= e($doctor['speciality']) ?>">
                <div class="doctor-card__visual">
                  <div class="doctor-card__avatar"><?= e(strtoupper(substr($doctor['fullName'], 0, 1))) ?></div>
                  <span class="doctor-card__status"><i></i> Available</span>
                </div>
                <div class="doctor-card__body">
                  <span class="doctor-card__speciality"><?= e($doctor['speciality']) ?></span>
                  <h3><?= e($doctor['fullName']) ?></h3>
                  <p><?= e($doctor['qualification']) ?> · <?= e(ucwords($doctor['address'])) ?></p>
                  <a href="patient/patient_login.php">Book appointment <span>→</span></a>
                </div>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <article class="doctor-card doctor-card--empty">
              <div class="doctor-card__body">
                <span class="doctor-card__speciality">SmartCare Network</span>
                <h3>Doctor directory</h3>
                <p>Sign in to your patient account to browse available doctors and specialties.</p>
                <a href="patient/patient_login.php">Open patient portal <span>→</span></a>
              </div>
            </article>
          <?php endif; ?>
          <div class="doctor-filter-empty" id="doctor-filter-empty" hidden>No doctors match that search yet.</div>
        </div>
      </div>
    </section>

    <section class="section__container about" id="aboutus">
      <div class="about__visual">
        <div class="about__image-wrap"><img src="../img/about.jpg" alt="SmartCare healthcare team" /></div>
        <div class="about__badge"><strong>One hub</strong><span>for smarter care coordination</span></div>
      </div>
      <div class="about__content">
        <div class="eyebrow eyebrow--dark"><span></span>About SmartCare Hub</div>
        <h2 class="section__header">Technology should make healthcare feel easier, not colder.</h2>
        <p>SmartCare Hub is designed to reduce friction around appointments by bringing patients, doctors, and administrators into one connected system.</p>
        <p>Instead of juggling disconnected processes, each role gets a focused portal for the actions that matter most—from booking and appointment management to doctor administration.</p>
        <div class="about__points">
          <div><span>✓</span><p><strong>Patient-centered</strong><small>Easy access to appointments and doctors.</small></p></div>
          <div><span>✓</span><p><strong>Role-based</strong><small>Dedicated experiences for every user type.</small></p></div>
          <div><span>✓</span><p><strong>Built for clarity</strong><small>Less clutter, clearer healthcare workflows.</small></p></div>
        </div>
      </div>
    </section>

    <section class="roles" id="roles">
      <div class="section__container">
        <div class="section__intro section__intro--center section__intro--light">
          <div class="eyebrow"><span></span>One platform, three perspectives</div>
          <h2 class="section__header">Choose your SmartCare workspace</h2>
          <p>Every role has a dedicated entry point while sharing the same connected appointment ecosystem.</p>
        </div>
        <div class="roles__grid">
          <a class="role-card" href="patient/patient_login.php"><span>Patient</span><h3>Manage your care journey</h3><p>Book and review appointments from your patient dashboard.</p><strong>Patient portal →</strong></a>
          <a class="role-card" href="doctor/doctor_login.php"><span>Doctor</span><h3>Focus on your appointments</h3><p>Access the doctor workspace and manage assigned appointments.</p><strong>Doctor portal →</strong></a>
          <a class="role-card" href="admin/admin_login.php"><span>Admin</span><h3>Coordinate the platform</h3><p>Manage doctors, approvals, and key SmartCare operations.</p><strong>Admin portal →</strong></a>
        </div>
      </div>
    </section>

    <section class="section__container final-cta">
      <div>
        <div class="eyebrow eyebrow--dark"><span></span>Ready when you are</div>
        <h2>Better appointment management starts here.</h2>
        <p>Create a patient account or sign in to continue your SmartCare journey.</p>
      </div>
      <div class="final-cta__actions">
        <a class="btn" href="patient/patient_registration.php">Create Patient Account</a>
        <a class="btn btn--outline" href="patient/patient_login.php">Sign In</a>
      </div>
    </section>
  </main>

  <?php include("footer.php"); ?>
  <script src="../js/front.js"></script>
  <script src="../js/system.js" defer></script>
</body>

</html>
