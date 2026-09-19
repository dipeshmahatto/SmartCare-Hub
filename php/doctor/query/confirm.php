<?php
require_once '../../security.php';
secure_session_start();
if (!isset($_SESSION['doctorloggedin']) || $_SESSION['doctorloggedin'] !== true) {
    header('Location: ../doctor_login.php');
    exit;
}
require_post_request('../doctor_dashboard.php');
require_valid_csrf('../doctor_dashboard.php');
include '../../database.php';
require_once '../../appointment_status.php';

$appointmentId = (int) ($_POST['id'] ?? 0);
$doctorName = clean_string($_SESSION['DoctorfullName'] ?? '');
$status = APPOINTMENT_CONFIRMED;
if ($appointmentId <= 0 || $doctorName === '') {
    header('Location: ../doctor_dashboard.php?error=' . urlencode('Invalid appointment.'));
    exit;
}

$stmt = $conn->prepare("UPDATE appointment SET status = ? WHERE aid = ? AND doctor = ? AND status = 'pending'");
$stmt->bind_param('sis', $status, $appointmentId, $doctorName);
$stmt->execute();
$changed = $stmt->affected_rows === 1;
$stmt->close();
header('Location: ../doctor_dashboard.php?' . ($changed ? 'confirmed=1' : 'error=' . urlencode('Appointment could not be confirmed.')));
exit;
