<?php
require_once '../../security.php';
secure_session_start();
if (!isset($_SESSION['Adminloggedin']) || $_SESSION['Adminloggedin'] !== true) {
    header('Location: ../admin_login.php');
    exit;
}
require_post_request('../approval.php');
require_valid_csrf('../approval.php');
include '../../database.php';

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../approval.php?error=' . urlencode('Invalid approval record.'));
    exit;
}

if (isset($_POST['reject'])) {
    $delete = $conn->prepare('DELETE FROM doctor_approval WHERE id = ?');
    $delete->bind_param('i', $id);
    $delete->execute();
    $delete->close();
    header('Location: ../approval.php?rejected=1');
    exit;
}

if (!isset($_POST['approve'])) {
    header('Location: ../approval.php');
    exit;
}

$select = $conn->prepare('SELECT fullName, email, phoneNumber, age, birthYear, address, speciality, qualification, password, gender FROM doctor_approval WHERE id = ? LIMIT 1');
$select->bind_param('i', $id);
$select->execute();
$doctor = $select->get_result()->fetch_assoc();
$select->close();

if (!$doctor) {
    header('Location: ../approval.php?error=' . urlencode('Doctor application no longer exists.'));
    exit;
}

$conn->begin_transaction();
try {
    $insert = $conn->prepare('INSERT INTO doctor (fullName, email, phoneNumber, age, birthYear, address, speciality, qualification, password, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $insert->bind_param(
        'sssiisssss',
        $doctor['fullName'],
        $doctor['email'],
        $doctor['phoneNumber'],
        $doctor['age'],
        $doctor['birthYear'],
        $doctor['address'],
        $doctor['speciality'],
        $doctor['qualification'],
        $doctor['password'],
        $doctor['gender']
    );
    $insert->execute();
    $insert->close();

    $delete = $conn->prepare('DELETE FROM doctor_approval WHERE id = ?');
    $delete->bind_param('i', $id);
    $delete->execute();
    $delete->close();
    $conn->commit();
    header('Location: ../approval.php?approved=1');
} catch (Throwable $error) {
    $conn->rollback();
    error_log('SmartCare doctor approval error: ' . $error->getMessage());
    header('Location: ../approval.php?error=' . urlencode('Doctor approval failed.'));
}
exit;
