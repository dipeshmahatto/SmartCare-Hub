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
$history = [];
$stmt = $conn->prepare(
    "SELECT a.*, d.qualification
     FROM appointment a
     LEFT JOIN doctor d ON d.fullName = a.doctor
     WHERE a.pid = ? AND a.status IN ('completed','cancelled')
     ORDER BY a.aid DESC"
);
if ($stmt) {
    $stmt->bind_param('i', $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $history[] = $row;
    $stmt->close();
}

$completedCount = 0;
$cancelledCount = 0;
foreach ($history as $record) {
    if ($record['status'] === APPOINTMENT_COMPLETED) $completedCount++;
    if ($record['status'] === APPOINTMENT_CANCELLED) $cancelledCount++;
}

$patientActivePage = 'history';
$pageTitle = 'Appointment History';
$pageEyebrow = 'Patient Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Appointment History | SmartCare</title>
</head>
<body>
<div class="container patient-shell">
    <?php include 'sidebar.php'; ?>
    <main class="content patient-content">
        <?php include 'top.php'; ?>

        <section class="patient-welcome">
            <div><span class="eyebrow">Care record</span><h1>Your appointment history</h1><p>Completed and cancelled bookings stay here so your appointment lifecycle remains visible.</p></div>
            <a class="primary-action" href="appointment.php"><i class="fa-solid fa-calendar-plus"></i> Book another visit</a>
        </section>

        <section class="history-summary-grid">
            <article><span class="history-summary-icon completed"><i class="fa-solid fa-check"></i></span><div><strong><?= $completedCount ?></strong><small>Completed visits</small></div></article>
            <article><span class="history-summary-icon cancelled"><i class="fa-solid fa-ban"></i></span><div><strong><?= $cancelledCount ?></strong><small>Cancelled bookings</small></div></article>
            <article><span class="history-summary-icon total"><i class="fa-solid fa-layer-group"></i></span><div><strong><?= count($history) ?></strong><small>Archived records</small></div></article>
        </section>

        <section class="history-panel">
            <div class="section-heading"><div><span class="eyebrow">Lifecycle archive</span><h2><?= count($history) ?> historical <?= count($history) === 1 ? 'record' : 'records' ?></h2></div></div>
            <?php if ($history): ?>
                <div class="history-list">
                    <?php foreach ($history as $appointment): $meta = appointmentStatusMeta($appointment['status']); ?>
                        <article class="history-card <?= $appointment['status'] === APPOINTMENT_CANCELLED ? 'history-card--cancelled' : '' ?>">
                            <div class="history-card__icon"><i class="<?= $appointment['status'] === APPOINTMENT_CANCELLED ? 'fa-solid fa-calendar-xmark' : 'fa-solid fa-stethoscope' ?>"></i></div>
                            <div class="history-card__body">
                                <div class="history-card__heading">
                                    <div><h3><?= htmlspecialchars($appointment['doctor']) ?></h3><p><?= htmlspecialchars($appointment['category']) ?><?= !empty($appointment['qualification']) ? ' · ' . htmlspecialchars($appointment['qualification']) : '' ?></p></div>
                                    <span class="status-pill <?= htmlspecialchars($meta['class']) ?>"><i class="<?= htmlspecialchars($meta['icon']) ?>"></i> <?= htmlspecialchars($meta['label']) ?></span>
                                </div>
                                <div class="history-card__meta">
                                    <span><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars(ucfirst(strtolower($appointment['day']))) ?></span>
                                    <span><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($appointment['app_time']) ?></span>
                                    <span><i class="fa-solid fa-hashtag"></i> <?= (int) $appointment['aid'] ?></span>
                                </div>
                                <p class="history-status-note"><?= htmlspecialchars($meta['description']) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state empty-state--history">
                    <div class="empty-state__icon"><i class="fa-solid fa-notes-medical"></i></div>
                    <h3>No historical appointments yet</h3>
                    <p>Completed or cancelled appointments will be preserved here.</p>
                    <a class="primary-action" href="patient_dashboard.php">Return to dashboard</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
  <script src="../../js/system.js" defer></script>
</body>
</html>
