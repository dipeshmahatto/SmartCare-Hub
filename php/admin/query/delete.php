<?php
require_once '../../security.php';
secure_session_start();
if (!isset($_SESSION['Adminloggedin']) || $_SESSION['Adminloggedin'] !== true) {
    header('Location: ../admin_login.php');
    exit;
}
require_post_request('../doctor_list.php');
require_valid_csrf('../doctor_list.php');
include '../../database.php';

$id = (int) ($_POST['id'] ?? 0);
$doctorFullName = clean_string($_POST['fullName'] ?? '');
if ($id <= 0 || $doctorFullName === '') {
    header('Location: ../doctor_list.php?error=' . urlencode('Invalid doctor record.'));
    exit;
}

$conn->begin_transaction();
try {
    $deleteAppointments = $conn->prepare('DELETE FROM appointment WHERE doctor = ?');
    $deleteAppointments->bind_param('s', $doctorFullName);
    $deleteAppointments->execute();
    $deleteAppointments->close();

    $deleteDoctor = $conn->prepare('DELETE FROM doctor WHERE id = ? AND fullName = ?');
    $deleteDoctor->bind_param('is', $id, $doctorFullName);
    $deleteDoctor->execute();
    $deleteDoctor->close();
    $conn->commit();
    header('Location: ../doctor_list.php?removed=1');
} catch (Throwable $error) {
    $conn->rollback();
    error_log('SmartCare doctor removal error: ' . $error->getMessage());
    header('Location: ../doctor_list.php?error=' . urlencode('Doctor could not be removed.'));
}
exit;
