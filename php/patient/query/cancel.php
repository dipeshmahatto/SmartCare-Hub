<?php
require_once '../../security.php';
secure_session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../patient_login.php');
    exit;
}
require_post_request('../patient_dashboard.php');
require_valid_csrf('../patient_dashboard.php');
include '../../database.php';
require_once '../../appointment_status.php';

$appointmentId = (int) ($_POST['id'] ?? 0);
$patientId = (int) ($_SESSION['id'] ?? 0);
if ($appointmentId <= 0 || $patientId <= 0) {
    header('Location: ../patient_dashboard.php?error=' . urlencode('Invalid appointment.'));
    exit;
}

$status = APPOINTMENT_CANCELLED;
$stmt = $conn->prepare("UPDATE appointment SET status = ? WHERE aid = ? AND pid = ? AND status IN ('pending','confirmed')");
$stmt->bind_param('sii', $status, $appointmentId, $patientId);
$stmt->execute();
$changed = $stmt->affected_rows === 1;
$stmt->close();

header('Location: ../patient_dashboard.php?' . ($changed ? 'cancelled=1' : 'error=' . urlencode('That appointment can no longer be cancelled.')));
exit;
