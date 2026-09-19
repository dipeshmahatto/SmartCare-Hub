<?php
require_once '../../security.php';
secure_session_start();
require_post_request('../patient_login.php');
require_valid_csrf('../patient_login.php');
include '../../database.php';

$phoneNumber = clean_string($_POST['phoneNumber'] ?? '');
$password = (string) ($_POST['password'] ?? '');

$stmt = $conn->prepare('SELECT id, fullName, address, age, gender, phoneNumber, password FROM patient WHERE phoneNumber = ? LIMIT 1');
if (!$stmt) {
    header('Location: ../patient_login.php?error=' . urlencode('Unable to sign in right now.'));
    exit;
}
$stmt->bind_param('s', $phoneNumber);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($row && verify_password_compatible($conn, 'patient', (int) $row['id'], $password, $row['password'])) {
    regenerate_authenticated_session();
    $_SESSION['id'] = (int) $row['id'];
    $_SESSION['fullName'] = $row['fullName'];
    $_SESSION['address'] = $row['address'];
    $_SESSION['age'] = $row['age'];
    $_SESSION['gender'] = $row['gender'];
    $_SESSION['phoneNumber'] = $row['phoneNumber'];
    $_SESSION['loggedin'] = true;
    header('Location: ../patient_dashboard.php');
    exit;
}

header('Location: ../patient_login.php?error=' . urlencode('Invalid credentials'));
exit;
