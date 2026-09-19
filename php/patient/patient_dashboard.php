<?php
require_once '../security.php';
secure_session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: patient_login.php');
    exit;
}
include 'query/session_values.php';
include '../database.php';
include 'query/image.php';
require_once '../appointment_status.php';

$patientId = (int) $id;
$activeAppointments = [];
$activeStmt = $conn->prepare(
    "SELECT a.*, d.id AS doctor_id, d.qualification
     FROM appointment a
     LEFT JOIN doctor d ON d.fullName = a.doctor
     WHERE a.pid = ? AND a.status IN ('pending','confirmed')
     ORDER BY FIELD(a.status, 'confirmed', 'pending'), a.aid DESC"
);
if ($activeStmt) {
    $activeStmt->bind_param('i', $patientId);
    $activeStmt->execute();
    $activeResult = $activeStmt->get_result();
    while ($row = $activeResult->fetch_assoc()) {
        $activeAppointments[] = $row;
    }
    $activeStmt->close();
}

$statusCounts = ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
$countStmt = $conn->prepare('SELECT status, COUNT(*) AS total FROM appointment WHERE pid = ? GROUP BY status');
if ($countStmt) {
    $countStmt->bind_param('i', $patientId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    while ($row = $countResult->fetch_assoc()) {
        if (isset($statusCounts[$row['status']])) {
            $statusCounts[$row['status']] = (int) $row['total'];
        }
    }
    $countStmt->close();
}

$specialtyCount = 0;
$specialtyStmt = $conn->prepare("SELECT COUNT(DISTINCT category) AS total FROM appointment WHERE pid = ? AND status <> 'cancelled'");
if ($specialtyStmt) {
    $specialtyStmt->bind_param('i', $patientId);
    $specialtyStmt->execute();
    if ($row = $specialtyStmt->get_result()->fetch_assoc()) {
        $specialtyCount = (int) $row['total'];
    }
    $specialtyStmt->close();
}

$activeCount = count($activeAppointments);
$featuredAppointment = $activeAppointments[0] ?? null;

function doctorAvatarUrl($doctorId)
{
    if (!$doctorId) return '';
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
        $file = __DIR__ . '/../doctor/uploads/' . $doctorId . '.' . $extension;
        if (file_exists($file)) return '../doctor/uploads/' . $doctorId . '.' . $extension;
    }
    return '';
}

function doctorInitials($name)
{
    $cleanName = trim(str_ireplace(['Dr.', 'Dr '], '', (string) $name));
    $parts = preg_split('/\s+/', $cleanName);
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        if ($part !== '') $initials .= strtoupper(substr($part, 0, 1));
    }
    return $initials ?: 'DR';
}

$patientActivePage = 'dashboard';
$pageTitle = 'Dashboard';
$pageEyebrow = 'Patient Portal';
$bookingPending = ($_GET['booking'] ?? '') === 'pending';
$cancelledNotice = ($_GET['cancelled'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Patient Dashboard | SmartCare</title>
</head>
<body>
<div class="container patient-shell">
    <?php include 'sidebar.php'; ?>

    <main class="content patient-content">
        <?php include 'top.php'; ?>

        <?php if ($bookingPending): ?>
            <div class="lifecycle-notice lifecycle-notice--pending"><i class="fa-regular fa-clock"></i><div><strong>Appointment request sent.</strong><span>Your booking is pending until the doctor confirms it.</span></div></div>
        <?php elseif ($cancelledNotice): ?>
            <div class="lifecycle-notice lifecycle-notice--cancelled"><i class="fa-solid fa-ban"></i><div><strong>Appointment cancelled.</strong><span>The record is preserved in your appointment history.</span></div></div>
        <?php endif; ?>

        <section class="patient-welcome">
            <div>
                <span class="eyebrow">Health overview</span>
                <h1>Welcome back, <?= htmlspecialchars(explode(' ', trim($fullName))[0]) ?>.</h1>
                <p>Track requests from booking through doctor confirmation and completed care.</p>
            </div>
            <a class="primary-action" href="appointment.php"><i class="fa-solid fa-calendar-plus"></i> Book appointment</a>
        </section>

        <section class="patient-stats" aria-label="Patient appointment statistics">
            <article class="stat-card stat-card--amber">
                <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
                <div><strong><?= $statusCounts['pending'] ?></strong><span>Pending confirmation</span></div>
            </article>
            <article class="stat-card stat-card--blue">
                <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <div><strong><?= $statusCounts['confirmed'] ?></strong><span>Confirmed appointments</span></div>
            </article>
            <article class="stat-card stat-card--green">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div><strong><?= $statusCounts['completed'] ?></strong><span>Completed visits</span></div>
            </article>
            <article class="stat-card stat-card--purple">
                <div class="stat-icon"><i class="fa-solid fa-stethoscope"></i></div>
                <div><strong><?= $specialtyCount ?></strong><span>Care specialties</span></div>
            </article>
        </section>

        <section class="dashboard-grid">
            <div class="dashboard-main">
                <div class="section-heading">
                    <div><span class="eyebrow">Your schedule</span><h2>Current appointment</h2></div>
                    <a href="history.php">View history <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <?php if ($featuredAppointment): ?>
                    <?php
                    $featuredDoctorAvatar = doctorAvatarUrl($featuredAppointment['doctor_id'] ?? null);
                    $featuredDoctorInitials = doctorInitials($featuredAppointment['doctor']);
                    $featuredStatus = appointmentStatusMeta($featuredAppointment['status']);
                    ?>
                    <article class="featured-appointment">
                        <div class="featured-accent"></div>
                        <div class="doctor-block">
                            <?php if ($featuredDoctorAvatar): ?>
                                <img src="<?= htmlspecialchars($featuredDoctorAvatar) ?>" alt="<?= htmlspecialchars($featuredAppointment['doctor']) ?>">
                            <?php else: ?>
                                <div class="doctor-avatar-fallback"><?= htmlspecialchars($featuredDoctorInitials) ?></div>
                            <?php endif; ?>
                            <div>
                                <span class="status-pill <?= htmlspecialchars($featuredStatus['class']) ?>"><i class="<?= htmlspecialchars($featuredStatus['icon']) ?>"></i> <?= htmlspecialchars($featuredStatus['label']) ?></span>
                                <h3><?= htmlspecialchars($featuredAppointment['doctor']) ?></h3>
                                <p><?= htmlspecialchars($featuredAppointment['category']) ?><?= !empty($featuredAppointment['qualification']) ? ' · ' . htmlspecialchars($featuredAppointment['qualification']) : '' ?></p>
                            </div>
                        </div>
                        <div class="appointment-facts">
                            <div><span><i class="fa-regular fa-calendar"></i> Day</span><strong><?= htmlspecialchars(ucfirst(strtolower($featuredAppointment['day']))) ?></strong></div>
                            <div><span><i class="fa-regular fa-clock"></i> Time</span><strong><?= htmlspecialchars($featuredAppointment['app_time']) ?></strong></div>
                            <div><span><i class="fa-solid fa-hashtag"></i> Booking</span><strong>#<?= (int) $featuredAppointment['aid'] ?></strong></div>
                        </div>
                        <div class="appointment-lifecycle" aria-label="Appointment lifecycle">
                            <div class="lifecycle-step is-complete"><span>1</span><div><strong>Booked</strong><small>Request created</small></div></div>
                            <div class="lifecycle-line <?= $featuredAppointment['status'] === APPOINTMENT_CONFIRMED ? 'is-complete' : '' ?>"></div>
                            <div class="lifecycle-step <?= $featuredAppointment['status'] === APPOINTMENT_CONFIRMED ? 'is-complete' : 'is-current' ?>"><span>2</span><div><strong>Confirmed</strong><small><?= $featuredAppointment['status'] === APPOINTMENT_CONFIRMED ? 'Doctor approved' : 'Waiting for doctor' ?></small></div></div>
                            <div class="lifecycle-line"></div>
                            <div class="lifecycle-step"><span>3</span><div><strong>Completed</strong><small>After consultation</small></div></div>
                        </div>
                        <button class="danger-ghost js-cancel-appointment" type="button" data-appointment-id="<?= (int) $featuredAppointment['aid'] ?>" data-doctor="<?= htmlspecialchars($featuredAppointment['doctor']) ?>">
                            <i class="fa-regular fa-calendar-xmark"></i> Cancel appointment
                        </button>
                    </article>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state__icon"><i class="fa-regular fa-calendar-check"></i></div>
                        <h3>Your active schedule is clear</h3>
                        <p>You have no pending or confirmed appointment right now.</p>
                        <a class="primary-action" href="appointment.php"><i class="fa-solid fa-plus"></i> Book your next appointment</a>
                    </div>
                <?php endif; ?>

                <?php if ($activeCount > 1): ?>
                    <div class="section-heading section-heading--secondary"><div><span class="eyebrow">More bookings</span><h2>Other active appointments</h2></div></div>
                    <div class="appointment-card-grid">
                        <?php foreach (array_slice($activeAppointments, 1) as $appointment): ?>
                            <?php
                            $avatar = doctorAvatarUrl($appointment['doctor_id'] ?? null);
                            $initials = doctorInitials($appointment['doctor']);
                            $meta = appointmentStatusMeta($appointment['status']);
                            ?>
                            <article class="appointment-card">
                                <div class="appointment-card__top">
                                    <?php if ($avatar): ?><img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($appointment['doctor']) ?>">
                                    <?php else: ?><div class="doctor-avatar-fallback doctor-avatar-fallback--small"><?= htmlspecialchars($initials) ?></div><?php endif; ?>
                                    <div><h3><?= htmlspecialchars($appointment['doctor']) ?></h3><p><?= htmlspecialchars($appointment['category']) ?></p></div>
                                    <span class="status-pill <?= htmlspecialchars($meta['class']) ?>"><i class="<?= htmlspecialchars($meta['icon']) ?>"></i> <?= htmlspecialchars($meta['label']) ?></span>
                                </div>
                                <div class="appointment-card__meta">
                                    <span><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars(ucfirst(strtolower($appointment['day']))) ?></span>
                                    <span><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($appointment['app_time']) ?></span>
                                </div>
                                <button class="text-danger js-cancel-appointment" type="button" data-appointment-id="<?= (int) $appointment['aid'] ?>" data-doctor="<?= htmlspecialchars($appointment['doctor']) ?>">Cancel</button>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="dashboard-side">
                <section class="profile-summary-card">
                    <div class="section-heading compact"><div><span class="eyebrow">Profile</span><h2>Your details</h2></div></div>
                    <dl class="profile-details">
                        <div><dt><i class="fa-solid fa-user"></i> Name</dt><dd><?= htmlspecialchars($fullName) ?></dd></div>
                        <div><dt><i class="fa-solid fa-phone"></i> Phone</dt><dd><?= htmlspecialchars($phoneNumber ?? 'Not available') ?></dd></div>
                        <div><dt><i class="fa-solid fa-location-dot"></i> Address</dt><dd><?= htmlspecialchars($address ?? 'Not available') ?></dd></div>
                        <div><dt><i class="fa-solid fa-cake-candles"></i> Age</dt><dd><?= htmlspecialchars($age ?? '—') ?></dd></div>
                    </dl>
                </section>

                <section class="quick-actions-card">
                    <div class="section-heading compact"><div><span class="eyebrow">Quick actions</span><h2>What would you like to do?</h2></div></div>
                    <a href="appointment.php"><span><i class="fa-solid fa-calendar-plus"></i></span><div><strong>Book a visit</strong><small>Request a doctor and available time.</small></div><i class="fa-solid fa-chevron-right"></i></a>
                    <a href="history.php"><span><i class="fa-solid fa-clock-rotate-left"></i></span><div><strong>Appointment history</strong><small>Review completed and cancelled records.</small></div><i class="fa-solid fa-chevron-right"></i></a>
                </section>
            </aside>
        </section>
    </main>
</div>

<div class="modal-backdrop" id="cancel-modal" hidden>
    <div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="cancel-title">
        <button class="modal-close" type="button" data-modal-close aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
        <h2 id="cancel-title">Cancel this appointment?</h2>
        <p id="cancel-message">The appointment will be marked cancelled and kept in your history.</p>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-modal-close>Keep appointment</button>
            <form method="post" action="query/cancel.php">
            <?= csrf_field() ?>
                <input type="hidden" name="id" id="cancel-appointment-id" value="">
                <button type="submit" name="cancel" class="danger-button">Yes, cancel</button>
            </form>
        </div>
    </div>
</div>

<script src="../../js/patient.js"></script>
  <script src="../../js/system.js" defer></script>
</body>
</html>
