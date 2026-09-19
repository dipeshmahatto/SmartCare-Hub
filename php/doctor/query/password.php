<?php
require_once '../../security.php';
secure_session_start();
require_post_request('../forgot.php');
require_valid_csrf('../password_change.php');
include '../../database.php';

$userId = (int) ($_SESSION['password_reset_user_id'] ?? 0);
$role = $_SESSION['password_reset_role'] ?? '';
$oldHash = (string) ($_SESSION['password_reset_old_hash'] ?? '');
$expires = (int) ($_SESSION['password_reset_expires'] ?? 0);
$newPassword = (string) ($_POST['newPassword'] ?? '');
$confirmPassword = (string) ($_POST['confirmPassword'] ?? '');

if ($role !== 'doctor' || $userId <= 0 || $expires < time()) {
    header('Location: ../forgot.php?error=' . urlencode('Password reset session expired. Please verify again.'));
    exit;
}
if (strlen($newPassword) < 8 || $newPassword !== $confirmPassword) {
    header('Location: ../password_change.php?error=' . urlencode('Use matching passwords with at least 8 characters.'));
    exit;
}
if ($oldHash !== '' && password_matches_existing($newPassword, $oldHash)) {
    header('Location: ../password_change.php?error=' . urlencode('Choose a password you have not just been using.'));
    exit;
}

$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
$stmt = $conn->prepare('UPDATE doctor SET password = ? WHERE id = ?');
$stmt->bind_param('si', $passwordHash, $userId);
$updated = $stmt->execute();
$stmt->close();

unset($_SESSION['password_reset_user_id'], $_SESSION['password_reset_role'], $_SESSION['password_reset_old_hash'], $_SESSION['password_reset_expires']);
if ($updated) {
    header('Location: ../doctor_login.php?password=changed');
    exit;
}
header('Location: ../forgot.php?error=' . urlencode('Password could not be changed.'));
exit;
