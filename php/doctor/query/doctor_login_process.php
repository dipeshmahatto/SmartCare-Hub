<?php
require_once '../../security.php';
secure_session_start();
require_post_request('../doctor_login.php');
require_valid_csrf('../doctor_login.php');
include '../../database.php';

$phoneNumber = clean_string($_POST['phoneNumber'] ?? '');
$password = (string) ($_POST['password'] ?? '');

$stmt = $conn->prepare('SELECT id, fullName, phoneNumber, password FROM doctor WHERE phoneNumber = ? LIMIT 1');
if (!$stmt) {
    header('Location: ../doctor_login.php?error=' . urlencode('Unable to sign in right now.'));
    exit;
}
$stmt->bind_param('s', $phoneNumber);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($row && verify_password_compatible($conn, 'doctor', (int) $row['id'], $password, $row['password'])) {
    regenerate_authenticated_session();
    $_SESSION['did'] = (int) $row['id'];
    $_SESSION['DoctorfullName'] = $row['fullName'];
    $_SESSION['phoneNumber'] = $row['phoneNumber'];
    $_SESSION['doctorloggedin'] = true;
    header('Location: ../doctor_dashboard.php');
    exit;
}

header('Location: ../doctor_login.php?error=' . urlencode('Invalid credentials'));
exit;
