<?php
require_once '../security.php';
secure_session_start();
if (!isset($_SESSION['doctorloggedin']) || $_SESSION['doctorloggedin'] !== true) {
    header('Location: doctor_login.php');
    exit;
}

include '../database.php';
include 'query/image.php';
require_once '../appointment_status.php';

$doctorId = isset($_SESSION['did']) ? (int) $_SESSION['did'] : 0;
$doctorName = $_SESSION['DoctorfullName'] ?? 'Doctor';
$doctor = ['fullName' => $doctorName, 'speciality' => 'Healthcare', 'qualification' => 'Medical Professional'];

$doctorStmt = $conn->prepare('SELECT fullName, speciality, qualification FROM doctor WHERE id = ? LIMIT 1');
if ($doctorStmt) {
    $doctorStmt->bind_param('i', $doctorId);
    $doctorStmt->execute();
    if ($row = $doctorStmt->get_result()->fetch_assoc()) {
        $doctor = array_merge($doctor, $row);
        $doctorName = $doctor['fullName'];
    }
    $doctorStmt->close();
}

$history = [];
$historyStmt = $conn->prepare(
    "SELECT a.aid, a.pid, a.category, a.app_time, a.day, a.status,
            p.fullName AS patient_name, p.email AS patient_email,
            p.phoneNumber AS patient_phone, p.age AS patient_age,
            p.gender AS patient_gender, p.address AS patient_address
     FROM appointment a
     LEFT JOIN patient p ON p.id = a.pid
     WHERE a.doctor = ? AND a.status IN ('completed','cancelled')
     ORDER BY a.aid DESC"
);
if ($historyStmt) {
    $historyStmt->bind_param('s', $doctorName);
    $historyStmt->execute();
    $result = $historyStmt->get_result();
    while ($row = $result->fetch_assoc()) $history[] = $row;
    $historyStmt->close();
}

$pendingCount = 0;
$confirmedCount = 0;
$activeStmt = $conn->prepare("SELECT status, COUNT(*) AS total FROM appointment WHERE doctor = ? AND status IN ('pending','confirmed') GROUP BY status");
if ($activeStmt) {
    $activeStmt->bind_param('s', $doctorName);
    $activeStmt->execute();
    $activeResult = $activeStmt->get_result();
    while ($row = $activeResult->fetch_assoc()) {
        if ($row['status'] === APPOINTMENT_PENDING) $pendingCount = (int) $row['total'];
        if ($row['status'] === APPOINTMENT_CONFIRMED) $confirmedCount = (int) $row['total'];
    }
    $activeStmt->close();
}

$completedCount = count(array_filter($history, static fn($item) => $item['status'] === APPOINTMENT_COMPLETED));
$cancelledCount = count(array_filter($history, static fn($item) => $item['status'] === APPOINTMENT_CANCELLED));
$activeCount = $pendingCount + $confirmedCount;
$uniquePatients = count(array_unique(array_map(static fn($item) => (int) $item['pid'], array_filter($history, static fn($item) => $item['status'] === APPOINTMENT_COMPLETED))));
$doctorInitials = '';
foreach (preg_split('/\s+/', trim(preg_replace('/^Dr\.?/i', '', $doctorName))) as $part) {
    if ($part !== '') $doctorInitials .= strtoupper(substr($part, 0, 1));
}
$doctorInitials = substr($doctorInitials ?: 'DR', 0, 2);

function e($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
function historyPatientInitials($name) {
    $initials = '';
    foreach (preg_split('/\s+/', trim((string) $name)) as $part) if ($part !== '') $initials .= strtoupper(substr($part, 0, 1));
    return substr($initials ?: 'PT', 0, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmartCare Hub doctor appointment lifecycle archive.">
    <link rel="stylesheet" href="../../css/doctor_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Appointment Archive | SmartCare Hub</title>
</head>
<body>
<div class="doctor-shell">
    <aside class="doctor-sidebar">
        <a class="doctor-brand" href="doctor_dashboard.php"><img src="../../img/logo.jpg" alt="SmartCare Hub"></a>
        <div class="doctor-profile">
            <?php if (!empty($imagePath)): ?><img class="doctor-avatar" src="<?= e($imagePath) ?>" alt="<?= e($doctorName) ?> profile">
            <?php else: ?><div class="doctor-avatar doctor-avatar-fallback"><?= e($doctorInitials) ?></div><?php endif; ?>
            <div><h2><?= e($doctorName) ?></h2><p><?= e($doctor['speciality']) ?></p><span class="doctor-verified"><i class="fa-solid fa-circle-check"></i> Verified doctor</span></div>
        </div>
        <nav class="doctor-nav" aria-label="Doctor navigation">
            <a href="doctor_dashboard.php"><i class="fa-solid fa-table-cells-large"></i><span>Workspace</span></a>
            <a href="doctor_dashboard.php#appointment-queue"><i class="fa-regular fa-calendar-check"></i><span>Lifecycle queue</span><b><?= $activeCount ?></b></a>
            <a href="history.php" class="active"><i class="fa-solid fa-clock-rotate-left"></i><span>Archive</span></a>
        </nav>
        <div class="sidebar-card"><i class="fa-solid fa-file-circle-check"></i><div><strong>Appointment archive</strong><p>Completed and cancelled records remain visible here.</p></div></div>
        <a class="sidebar-logout" href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign out</a>
    </aside>

    <main class="doctor-main">
        <header class="workspace-topbar">
            <div class="topbar-brand"><img src="../../img/logo.png" alt="SmartCare Hub logo"><div><span>SmartCare Hub</span><strong>Appointment Archive</strong></div></div>
            <div class="topbar-actions"><a href="doctor_dashboard.php" class="topbar-button"><i class="fa-solid fa-arrow-left"></i><span>Back to workspace</span></a></div>
        </header>

        <section class="history-hero">
            <div><span class="hero-eyebrow"><i class="fa-solid fa-clock-rotate-left"></i> Lifecycle archive</span><h1>Appointment history</h1><p>Completed consultations and cancelled bookings stay visible instead of disappearing from the system.</p></div>
            <div class="history-hero-number"><span>Archived</span><strong><?= count($history) ?></strong><small>lifecycle records</small></div>
        </section>

        <section class="doctor-stats history-stats">
            <article class="doctor-stat-card stat-green"><div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div><div><span>Completed</span><strong><?= $completedCount ?></strong><small>Finished consultations</small></div></article>
            <article class="doctor-stat-card stat-red"><div class="stat-icon"><i class="fa-solid fa-ban"></i></div><div><span>Cancelled</span><strong><?= $cancelledCount ?></strong><small>Preserved booking records</small></div></article>
            <article class="doctor-stat-card stat-violet"><div class="stat-icon"><i class="fa-solid fa-user-group"></i></div><div><span>Patients served</span><strong><?= $uniquePatients ?></strong><small>Unique completed patient records</small></div></article>
            <article class="doctor-stat-card stat-blue"><div class="stat-icon"><i class="fa-regular fa-calendar-check"></i></div><div><span>Still active</span><strong><?= $activeCount ?></strong><small><?= $pendingCount ?> pending · <?= $confirmedCount ?> confirmed</small></div></article>
        </section>

        <section class="workspace-panel history-panel">
            <div class="panel-heading queue-heading"><div><span class="panel-kicker">Lifecycle records</span><h2>Appointment archive</h2><p>Open a record to review the patient snapshot and booking details.</p></div><span class="count-pill"><?= count($history) ?> records</span></div>

            <?php if ($history): ?>
                <div class="history-list">
                    <?php foreach ($history as $appointment): $meta = appointmentStatusMeta($appointment['status']); ?>
                        <article class="history-row history-row--<?= e($appointment['status']) ?>">
                            <span class="patient-avatar history-avatar"><?= e(historyPatientInitials($appointment['patient_name'])) ?></span>
                            <div class="history-patient"><strong><?= e($appointment['patient_name'] ?: 'Patient') ?></strong><small>Patient #<?= (int) $appointment['pid'] ?> • Appointment #SC-<?= (int) $appointment['aid'] ?></small></div>
                            <div class="history-meta"><span><i class="fa-solid fa-notes-medical"></i><?= e($appointment['category']) ?></span><span><i class="fa-regular fa-calendar"></i><?= e(ucfirst(strtolower($appointment['day']))) ?></span><span><i class="fa-regular fa-clock"></i><?= e($appointment['app_time']) ?></span></div>
                            <span class="status-chip <?= e($meta['class']) ?>"><i class="<?= e($meta['icon']) ?>"></i> <?= e($meta['label']) ?></span>
                            <button type="button" class="detail-button patient-detail-trigger"
                                    data-patient-name="<?= e($appointment['patient_name'] ?: 'Patient') ?>" data-patient-email="<?= e($appointment['patient_email']) ?>"
                                    data-patient-phone="<?= e($appointment['patient_phone']) ?>" data-patient-age="<?= e($appointment['patient_age']) ?>"
                                    data-patient-gender="<?= e($appointment['patient_gender']) ?>" data-patient-address="<?= e($appointment['patient_address']) ?>"
                                    data-category="<?= e($appointment['category']) ?>" data-day="<?= e($appointment['day']) ?>" data-time="<?= e($appointment['app_time']) ?>"
                                    data-status="<?= e($meta['label']) ?>" data-id="<?= (int) $appointment['aid'] ?>"><i class="fa-regular fa-eye"></i> View</button>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="workspace-empty-state"><div class="empty-illustration"><i class="fa-solid fa-clock-rotate-left"></i></div><span>No archived appointments yet</span><h3>Your history will build automatically.</h3><p>Completed or cancelled appointments will appear here.</p><a href="doctor_dashboard.php"><i class="fa-solid fa-arrow-left"></i> Return to workspace</a></div>
            <?php endif; ?>
        </section>

        <footer class="workspace-footer"><span>SmartCare Hub Doctor Workspace</span><span><i class="fa-solid fa-shield-halved"></i> Lifecycle records preserved</span></footer>
    </main>
</div>

<div class="workspace-modal" id="patient-detail-modal" aria-hidden="true">
    <div class="modal-backdrop" data-close-modal></div>
    <section class="modal-card" role="dialog" aria-modal="true" aria-labelledby="patient-modal-title">
        <button type="button" class="modal-close" data-close-modal aria-label="Close patient details"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-header"><span class="modal-icon"><i class="fa-solid fa-user-injured"></i></span><div><small>Patient snapshot</small><h2 id="patient-modal-title">Patient details</h2><p id="modal-appointment-id"></p></div></div>
        <div class="patient-modal-grid"><div><span>Name</span><strong id="modal-patient-name">—</strong></div><div><span>Age / Gender</span><strong id="modal-patient-demographic">—</strong></div><div><span>Phone</span><strong id="modal-patient-phone">—</strong></div><div><span>Email</span><strong id="modal-patient-email">—</strong></div><div class="modal-wide"><span>Address</span><strong id="modal-patient-address">—</strong></div></div>
        <div class="appointment-modal-summary"><div><i class="fa-solid fa-notes-medical"></i><span><small>Specialty</small><strong id="modal-category">—</strong></span></div><div><i class="fa-regular fa-calendar"></i><span><small>Day</small><strong id="modal-day">—</strong></span></div><div><i class="fa-regular fa-clock"></i><span><small>Time</small><strong id="modal-time">—</strong></span></div><div><i class="fa-solid fa-wave-square"></i><span><small>Status</small><strong id="modal-status">—</strong></span></div></div>
    </section>
</div>
<script src="../../js/doctor-workspace.js"></script>
  <script src="../../js/system.js" defer></script>
</body>
</html>
