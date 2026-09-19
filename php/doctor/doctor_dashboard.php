<?php
require_once '../security.php';
secure_session_start();

if (!isset($_SESSION['doctorloggedin']) || $_SESSION['doctorloggedin'] !== true) {
    header('Location: doctor_login.php');
    exit;
}

include('../database.php');
include('query/image.php');
require_once '../appointment_status.php';

$doctorId = isset($_SESSION['did']) ? (int) $_SESSION['did'] : 0;
$doctorName = $_SESSION['DoctorfullName'] ?? 'Doctor';

$doctor = [
    'fullName' => $doctorName,
    'email' => '',
    'phoneNumber' => '',
    'age' => '',
    'address' => '',
    'speciality' => 'Healthcare',
    'qualification' => 'Medical Professional',
    'gender' => ''
];

$doctorStmt = $conn->prepare('SELECT fullName, email, phoneNumber, age, address, speciality, qualification, gender FROM doctor WHERE id = ? LIMIT 1');
if ($doctorStmt) {
    $doctorStmt->bind_param('i', $doctorId);
    $doctorStmt->execute();
    $doctorResult = $doctorStmt->get_result();
    if ($doctorRow = $doctorResult->fetch_assoc()) {
        $doctor = array_merge($doctor, $doctorRow);
        $doctorName = $doctor['fullName'];
    }
    $doctorStmt->close();
}

$appointments = [];
$activeStmt = $conn->prepare(
    "SELECT a.aid, a.pid, a.category, a.app_time, a.day, a.status,
            p.fullName AS patient_name, p.email AS patient_email,
            p.phoneNumber AS patient_phone, p.age AS patient_age,
            p.gender AS patient_gender, p.address AS patient_address
     FROM appointment a
     LEFT JOIN patient p ON p.id = a.pid
     WHERE a.doctor = ? AND a.status IN ('pending','confirmed')
     ORDER BY FIELD(a.status, 'pending', 'confirmed'),
              FIELD(a.day, 'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'),
              FIELD(a.app_time, '8 AM','9 AM','10 AM','11 AM','12 PM','1 PM','2 PM','3 PM','4 PM','5 PM','6 PM'),
              a.aid ASC"
);
if ($activeStmt) {
    $activeStmt->bind_param('s', $doctorName);
    $activeStmt->execute();
    $activeResult = $activeStmt->get_result();
    while ($row = $activeResult->fetch_assoc()) {
        $appointments[] = $row;
    }
    $activeStmt->close();
}

$completedCount = 0;
$cancelledCount = 0;
$totalPatients = 0;
$statsStmt = $conn->prepare(
    "SELECT SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_count,
            COUNT(DISTINCT pid) AS patient_count
     FROM appointment WHERE doctor = ?"
);
if ($statsStmt) {
    $statsStmt->bind_param('s', $doctorName);
    $statsStmt->execute();
    $statsResult = $statsStmt->get_result();
    if ($stats = $statsResult->fetch_assoc()) {
        $completedCount = (int) ($stats['completed_count'] ?? 0);
        $cancelledCount = (int) ($stats['cancelled_count'] ?? 0);
        $totalPatients = (int) ($stats['patient_count'] ?? 0);
    }
    $statsStmt->close();
}

$pendingCount = count(array_filter($appointments, static fn($appointment) => $appointment['status'] === APPOINTMENT_PENDING));
$confirmedCount = count(array_filter($appointments, static fn($appointment) => $appointment['status'] === APPOINTMENT_CONFIRMED));
$todayName = strtoupper(date('l'));
$todayAppointments = array_values(array_filter($appointments, static function ($appointment) use ($todayName) {
    return strtoupper($appointment['day'] ?? '') === $todayName;
}));

$activeCount = count($appointments);
$todayCount = count($todayAppointments);
$totalLifecycleVisits = $activeCount + $completedCount;
$completionRate = $totalLifecycleVisits > 0 ? (int) round(($completedCount / $totalLifecycleVisits) * 100) : 0;

$hour = (int) date('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$firstName = trim(preg_replace('/^Dr\.?\s*/i', '', $doctorName));
$firstNameParts = preg_split('/\s+/', $firstName);
$displayFirstName = $firstNameParts[0] ?? $doctorName;
$doctorInitials = '';
foreach (preg_split('/\s+/', trim(preg_replace('/^Dr\.?/i', '', $doctorName))) as $namePart) {
    if ($namePart !== '') {
        $doctorInitials .= strtoupper(substr($namePart, 0, 1));
    }
}
$doctorInitials = substr($doctorInitials ?: 'DR', 0, 2);

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function patientInitials($name)
{
    $initials = '';
    foreach (preg_split('/\s+/', trim((string) $name)) as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    return substr($initials ?: 'PT', 0, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmartCare Hub doctor workspace for managing appointments and patient visits.">
    <link rel="stylesheet" href="../../css/doctor_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Doctor Workspace | SmartCare Hub</title>
</head>
<body>
<div class="doctor-shell">
    <aside class="doctor-sidebar">
        <a class="doctor-brand" href="doctor_dashboard.php" aria-label="SmartCare Hub Doctor Workspace">
            <img src="../../img/logo.jpg" alt="SmartCare Hub">
        </a>

        <div class="doctor-profile">
            <?php if (!empty($imagePath)): ?>
                <img class="doctor-avatar" src="<?= e($imagePath) ?>" alt="<?= e($doctorName) ?> profile">
            <?php else: ?>
                <div class="doctor-avatar doctor-avatar-fallback" aria-label="<?= e($doctorName) ?> initials"><?= e($doctorInitials) ?></div>
            <?php endif; ?>
            <div>
                <h2><?= e($doctorName) ?></h2>
                <p><?= e($doctor['speciality']) ?></p>
                <span class="doctor-verified"><i class="fa-solid fa-circle-check"></i> Verified doctor</span>
            </div>
        </div>

        <?php if (empty($imagePath)): ?>
            <form class="sidebar-upload" action="upload.php" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
                <label for="doctor-profile-file"><i class="fa-regular fa-image"></i> Add profile photo</label>
                <input type="file" name="file" id="doctor-profile-file" accept="image/jpeg,image/png,image/webp" required>
                <button type="submit">Upload</button>
            </form>
        <?php endif; ?>

        <nav class="doctor-nav" aria-label="Doctor navigation">
            <a href="doctor_dashboard.php" class="active"><i class="fa-solid fa-table-cells-large"></i><span>Workspace</span></a>
            <a href="#appointment-queue"><i class="fa-regular fa-calendar-check"></i><span>Lifecycle queue</span><b><?= $activeCount ?></b></a>
            <a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i><span>Appointment archive</span></a>
        </nav>

        <div class="sidebar-card">
            <i class="fa-solid fa-shield-heart"></i>
            <div>
                <strong>SmartCare workspace</strong>
                <p>Keep appointments organized and patient visits moving.</p>
            </div>
        </div>

        <a class="sidebar-logout" href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign out</a>
    </aside>

    <main class="doctor-main">
        <header class="workspace-topbar">
            <div class="topbar-brand">
                <img src="../../img/logo.png" alt="SmartCare Hub logo">
                <div>
                    <span>SmartCare Hub</span>
                    <strong>Doctor Workspace</strong>
                </div>
            </div>
            <div class="topbar-actions">
                <button class="theme-toggle" type="button" data-theme-toggle><span data-theme-icon aria-hidden="true"></span><span class="theme-toggle__label">Theme</span></button>
                <span class="workspace-date"><i class="fa-regular fa-calendar"></i><?= e(date('l, M j')) ?></span>
                <a href="history.php" class="topbar-button"><i class="fa-solid fa-clock-rotate-left"></i><span>History</span></a>
            </div>
        </header>

        <section class="doctor-hero">
            <div>
                <span class="hero-eyebrow"><i class="fa-solid fa-stethoscope"></i> <?= e($doctor['speciality']) ?> workspace</span>
                <h1><?= e($greeting) ?>, Dr. <?= e($displayFirstName) ?>.</h1>
                <p>You have <strong><?= $pendingCount ?></strong> pending <?= $pendingCount === 1 ? 'request' : 'requests' ?>, <strong><?= $confirmedCount ?></strong> confirmed <?= $confirmedCount === 1 ? 'visit' : 'visits' ?> and <strong><?= $todayCount ?></strong> active slots on <?= e(ucfirst(strtolower($todayName))) ?>.</p>
                <div class="hero-actions">
                    <a href="#appointment-queue" class="primary-action"><i class="fa-solid fa-list-check"></i> Open patient queue</a>
                    <a href="history.php" class="secondary-action"><i class="fa-regular fa-circle-check"></i> Appointment archive</a>
                </div>
            </div>
            <div class="hero-doctor-card">
                <div class="hero-doctor-icon"><i class="fa-solid fa-user-doctor"></i></div>
                <span>Your profile</span>
                <strong><?= e($doctor['qualification']) ?></strong>
                <small><?= e($doctor['address']) ?: 'SmartCare Hub' ?></small>
            </div>
        </section>

        <section class="doctor-stats" aria-label="Doctor appointment lifecycle overview">
            <article class="doctor-stat-card stat-amber">
                <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                <div><span>Pending</span><strong><?= $pendingCount ?></strong><small>Requests awaiting confirmation</small></div>
            </article>
            <article class="doctor-stat-card stat-blue">
                <div class="stat-icon"><i class="fa-regular fa-calendar-check"></i></div>
                <div><span>Confirmed</span><strong><?= $confirmedCount ?></strong><small>Visits ready for consultation</small></div>
            </article>
            <article class="doctor-stat-card stat-green">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div><span>Completed</span><strong><?= $completedCount ?></strong><small><?= $completionRate ?>% of active + completed records</small></div>
            </article>
            <article class="doctor-stat-card stat-violet">
                <div class="stat-icon"><i class="fa-solid fa-user-group"></i></div>
                <div><span>Patients</span><strong><?= $totalPatients ?></strong><small><?= $cancelledCount ?> cancelled bookings preserved</small></div>
            </article>
        </section>

        <section class="workspace-grid">
            <article class="workspace-panel today-panel">
                <div class="panel-heading">
                    <div>
                        <span class="panel-kicker">Today • <?= e($todayName) ?></span>
                        <h2>Today's patient queue</h2>
                    </div>
                    <span class="count-pill"><?= $todayCount ?> <?= $todayCount === 1 ? 'visit' : 'visits' ?></span>
                </div>

                <?php if ($todayCount > 0): ?>
                    <div class="today-list">
                        <?php foreach (array_slice($todayAppointments, 0, 4) as $appointment): ?>
                            <button type="button" class="today-visit patient-detail-trigger"
                                    data-patient-name="<?= e($appointment['patient_name'] ?: 'Patient') ?>"
                                    data-patient-email="<?= e($appointment['patient_email']) ?>"
                                    data-patient-phone="<?= e($appointment['patient_phone']) ?>"
                                    data-patient-age="<?= e($appointment['patient_age']) ?>"
                                    data-patient-gender="<?= e($appointment['patient_gender']) ?>"
                                    data-patient-address="<?= e($appointment['patient_address']) ?>"
                                    data-category="<?= e($appointment['category']) ?>"
                                    data-day="<?= e($appointment['day']) ?>"
                                    data-time="<?= e($appointment['app_time']) ?>"
                                    data-status="<?= e(appointmentStatusMeta($appointment['status'])['label']) ?>"
                                    data-id="<?= (int) $appointment['aid'] ?>">
                                <span class="patient-mini-avatar"><?= e(patientInitials($appointment['patient_name'])) ?></span>
                                <span class="today-visit-copy">
                                    <strong><?= e($appointment['patient_name'] ?: 'Patient') ?></strong>
                                    <small><?= e($appointment['category']) ?> • #SC-<?= (int) $appointment['aid'] ?></small>
                                </span>
                                <?php $todayStatus = appointmentStatusMeta($appointment['status']); ?>
                                <span class="today-status <?= e($todayStatus['class']) ?>"><?= e($todayStatus['label']) ?></span>
                                <span class="today-time"><?= e($appointment['app_time']) ?></span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="compact-empty-state">
                        <div class="empty-icon"><i class="fa-regular fa-calendar-check"></i></div>
                        <div><strong>No appointments on today's weekday.</strong><p>Your full pending and confirmed queue is available below.</p></div>
                    </div>
                <?php endif; ?>
            </article>

            <aside class="workspace-panel profile-panel">
                <div class="panel-heading compact">
                    <div><span class="panel-kicker">Professional profile</span><h2>Doctor details</h2></div>
                </div>
                <div class="profile-detail-list">
                    <div><i class="fa-solid fa-graduation-cap"></i><span><small>Qualification</small><strong><?= e($doctor['qualification']) ?></strong></span></div>
                    <div><i class="fa-solid fa-stethoscope"></i><span><small>Specialty</small><strong><?= e($doctor['speciality']) ?></strong></span></div>
                    <div><i class="fa-regular fa-envelope"></i><span><small>Email</small><strong><?= e($doctor['email']) ?></strong></span></div>
                    <div><i class="fa-solid fa-location-dot"></i><span><small>Location</small><strong><?= e($doctor['address']) ?></strong></span></div>
                </div>
            </aside>
        </section>

        <section class="workspace-panel queue-panel" id="appointment-queue">
            <div class="panel-heading queue-heading">
                <div>
                    <span class="panel-kicker">Live workspace</span>
                    <h2>Appointment lifecycle queue</h2>
                    <p>Confirm new requests first, then close confirmed visits after consultation.</p>
                </div>
                <div class="queue-summary"><i class="fa-solid fa-wave-square"></i><span><strong><?= $activeCount ?></strong> waiting</span></div>
            </div>

            <?php if ($activeCount > 0): ?>
                <div class="appointment-queue-list">
                    <?php foreach ($appointments as $index => $appointment): ?>
                        <?php $statusMeta = appointmentStatusMeta($appointment['status']); ?>
                        <article class="appointment-row appointment-row--<?= e($appointment['status']) ?>">
                            <div class="queue-position"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></div>
                            <div class="patient-identity">
                                <span class="patient-avatar"><?= e(patientInitials($appointment['patient_name'])) ?></span>
                                <div>
                                    <strong><?= e($appointment['patient_name'] ?: 'Patient') ?></strong>
                                    <small>Patient ID #<?= (int) $appointment['pid'] ?></small>
                                </div>
                            </div>
                            <div class="appointment-meta">
                                <span><i class="fa-regular fa-calendar"></i><strong><?= e(ucfirst(strtolower($appointment['day']))) ?></strong></span>
                                <span><i class="fa-regular fa-clock"></i><strong><?= e($appointment['app_time']) ?></strong></span>
                                <span><i class="fa-solid fa-notes-medical"></i><strong><?= e($appointment['category']) ?></strong></span>
                            </div>
                            <span class="status-chip <?= e($statusMeta['class']) ?>"><i class="<?= e($statusMeta['icon']) ?>"></i> <?= e($statusMeta['label']) ?></span>
                            <div class="queue-actions">
                                <button type="button" class="detail-button patient-detail-trigger"
                                        data-patient-name="<?= e($appointment['patient_name'] ?: 'Patient') ?>"
                                        data-patient-email="<?= e($appointment['patient_email']) ?>"
                                        data-patient-phone="<?= e($appointment['patient_phone']) ?>"
                                        data-patient-age="<?= e($appointment['patient_age']) ?>"
                                        data-patient-gender="<?= e($appointment['patient_gender']) ?>"
                                        data-patient-address="<?= e($appointment['patient_address']) ?>"
                                        data-category="<?= e($appointment['category']) ?>"
                                        data-day="<?= e($appointment['day']) ?>"
                                        data-time="<?= e($appointment['app_time']) ?>"
                                        data-status="<?= e($statusMeta['label']) ?>"
                                        data-id="<?= (int) $appointment['aid'] ?>">
                                    <i class="fa-regular fa-eye"></i> Details
                                </button>
                                <?php if ($appointment['status'] === APPOINTMENT_PENDING): ?>
                                    <button type="button" class="confirm-button confirm-trigger" data-id="<?= (int) $appointment['aid'] ?>" data-patient="<?= e($appointment['patient_name'] ?: 'Patient') ?>">
                                        <i class="fa-solid fa-calendar-check"></i> Confirm
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="complete-button complete-trigger" data-id="<?= (int) $appointment['aid'] ?>" data-patient="<?= e($appointment['patient_name'] ?: 'Patient') ?>">
                                        <i class="fa-solid fa-check"></i> Mark completed
                                    </button>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="workspace-empty-state">
                    <div class="empty-illustration"><i class="fa-solid fa-clipboard-check"></i></div>
                    <span>Queue cleared</span>
                    <h3>You're all caught up.</h3>
                    <p>There are no pending or confirmed appointments in your queue right now.</p>
                    <a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> View appointment archive</a>
                </div>
            <?php endif; ?>
        </section>

        <footer class="workspace-footer">
            <span>SmartCare Hub Doctor Workspace</span>
            <span><i class="fa-solid fa-shield-halved"></i> Role-based doctor access</span>
        </footer>
    </main>
</div>

<div class="workspace-modal" id="patient-detail-modal" aria-hidden="true">
    <div class="modal-backdrop" data-close-modal></div>
    <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="patient-modal-title">
        <button type="button" class="modal-close" data-close-modal aria-label="Close patient details"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-header">
            <span class="modal-icon"><i class="fa-solid fa-user-injured"></i></span>
            <div><small>Patient snapshot</small><h2 id="patient-modal-title">Patient details</h2><p id="modal-appointment-id"></p></div>
        </div>
        <div class="patient-modal-grid">
            <div><span>Name</span><strong id="modal-patient-name">—</strong></div>
            <div><span>Age / Gender</span><strong id="modal-patient-demographic">—</strong></div>
            <div><span>Phone</span><strong id="modal-patient-phone">—</strong></div>
            <div><span>Email</span><strong id="modal-patient-email">—</strong></div>
            <div class="modal-wide"><span>Address</span><strong id="modal-patient-address">—</strong></div>
        </div>
        <div class="appointment-modal-summary">
            <div><i class="fa-solid fa-notes-medical"></i><span><small>Specialty</small><strong id="modal-category">—</strong></span></div>
            <div><i class="fa-regular fa-calendar"></i><span><small>Day</small><strong id="modal-day">—</strong></span></div>
            <div><i class="fa-regular fa-clock"></i><span><small>Time</small><strong id="modal-time">—</strong></span></div>
            <div><i class="fa-solid fa-wave-square"></i><span><small>Status</small><strong id="modal-status">—</strong></span></div>
        </div>
    </section>
</div>

<div class="workspace-modal" id="confirm-modal" aria-hidden="true">
    <div class="modal-backdrop" data-close-confirm></div>
    <section class="modal-card complete-modal-card confirm-modal-card" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
        <button type="button" class="modal-close" data-close-confirm aria-label="Close confirmation"><i class="fa-solid fa-xmark"></i></button>
        <span class="completion-icon confirmation-icon"><i class="fa-solid fa-calendar-check"></i></span>
        <small>Accept appointment request</small>
        <h2 id="confirm-modal-title">Confirm this appointment?</h2>
        <p id="confirm-modal-copy">The patient will see this booking as confirmed and ready for consultation.</p>
        <form method="post" action="query/confirm.php" id="confirm-form">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="confirm-appointment-id" value="">
            <div class="modal-actions">
                <button type="button" class="modal-cancel" data-close-confirm>Not yet</button>
                <button type="submit" class="modal-confirm modal-confirm--blue"><i class="fa-solid fa-calendar-check"></i> Confirm appointment</button>
            </div>
        </form>
    </section>
</div>

<div class="workspace-modal" id="complete-modal" aria-hidden="true">
    <div class="modal-backdrop" data-close-complete></div>
    <section class="modal-card complete-modal-card" role="dialog" aria-modal="true" aria-labelledby="complete-modal-title">
        <button type="button" class="modal-close" data-close-complete aria-label="Close confirmation"><i class="fa-solid fa-xmark"></i></button>
        <span class="completion-icon"><i class="fa-solid fa-check"></i></span>
        <small>Complete consultation</small>
        <h2 id="complete-modal-title">Mark this visit as completed?</h2>
        <p id="complete-modal-copy">This confirmed appointment will move into the completed appointment archive.</p>
        <form method="post" action="query/completed.php" id="complete-form">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="complete-appointment-id" value="">
            <div class="modal-actions">
                <button type="button" class="modal-cancel" data-close-complete>Keep active</button>
                <button type="submit" name="completed" class="modal-confirm"><i class="fa-solid fa-check"></i> Yes, complete visit</button>
            </div>
        </form>
    </section>
</div>

<script src="../../js/doctor-workspace.js"></script>
  <script src="../../js/system.js" defer></script>
</body>
</html>
