<?php
require_once '../../security.php';
secure_session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../patient_login.php');
    exit;
}
require_post_request('../appointment.php');
require_valid_csrf('../appointment.php');
include '../../database.php';
require_once '../../appointment_status.php';

$patientId = (int) ($_SESSION['id'] ?? 0);
$category = clean_string($_POST['category'] ?? '');
$doctor = clean_string($_POST['doctor'] ?? '');
$time = clean_string($_POST['time'] ?? '');
$day = strtoupper(clean_string($_POST['day'] ?? ''));
$allowedDays = ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY'];

if ($patientId <= 0 || $category === '' || $doctor === '' || $time === '' || !in_array($day, $allowedDays, true)) {
    header('Location: ../appointment.php?error=' . urlencode('Please complete every booking step.'));
    exit;
}

$doctorStmt = $conn->prepare('SELECT id FROM doctor WHERE fullName = ? AND speciality = ? LIMIT 1');
$doctorStmt->bind_param('ss', $doctor, $category);
$doctorStmt->execute();
$doctorExists = $doctorStmt->get_result()->num_rows === 1;
$doctorStmt->close();
if (!$doctorExists) {
    header('Location: ../appointment.php?error=' . urlencode('The selected doctor does not match that specialty.'));
    exit;
}

$timeStmt = $conn->prepare('SELECT 1 FROM times WHERE times = ? LIMIT 1');
$timeStmt->bind_param('s', $time);
$timeStmt->execute();
$timeExists = $timeStmt->get_result()->num_rows === 1;
$timeStmt->close();
if (!$timeExists) {
    header('Location: ../appointment.php?error=' . urlencode('The selected time slot is not valid.'));
    exit;
}

$conn->begin_transaction();
try {
    $slotStmt = $conn->prepare("SELECT aid FROM appointment WHERE doctor = ? AND day = ? AND app_time = ? AND status IN ('pending','confirmed') LIMIT 1 FOR UPDATE");
    $slotStmt->bind_param('sss', $doctor, $day, $time);
    $slotStmt->execute();
    $slotTaken = $slotStmt->get_result()->num_rows > 0;
    $slotStmt->close();
    if ($slotTaken) {
        $conn->rollback();
        header('Location: ../appointment.php?error=' . urlencode('That time was just booked. Please choose another available slot.'));
        exit;
    }

    $status = APPOINTMENT_PENDING;
    $stmt = $conn->prepare('INSERT INTO appointment (pid, category, doctor, app_time, day, status) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isssss', $patientId, $category, $doctor, $time, $day, $status);
    $stmt->execute();
    $stmt->close();
    $conn->commit();

    header('Location: ../patient_dashboard.php?booking=pending');
    exit;
} catch (Throwable $error) {
    $conn->rollback();
    error_log('SmartCare booking error: ' . $error->getMessage());
    header('Location: ../appointment.php?error=' . urlencode('Booking was unsuccessful. Please try again.'));
    exit;
}
