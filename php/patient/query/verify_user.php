<?php
require_once '../../security.php';
secure_session_start();
require_post_request('../forgot.php');
require_valid_csrf('../forgot.php');
include '../../database.php';

$phoneNumber = clean_string($_POST['phoneNumber'] ?? '');
$birthYear = (int) ($_POST['birthYear'] ?? 0);
$stmt = $conn->prepare('SELECT id, password FROM patient WHERE phoneNumber = ? AND birthYear = ? LIMIT 1');
$stmt->bind_param('si', $phoneNumber, $birthYear);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($row) {
    $_SESSION['password_reset_user_id'] = (int) $row['id'];
    $_SESSION['password_reset_role'] = 'patient';
    $_SESSION['password_reset_old_hash'] = $row['password'];
    $_SESSION['password_reset_expires'] = time() + 600;
    header('Location: ../password_change.php');
    exit;
}
header('Location: ../forgot.php?error=' . urlencode('Phone number and birth year do not match.'));
exit;
