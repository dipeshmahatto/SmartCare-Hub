<?php
require_once '../../security.php';
secure_session_start();
require_post_request('../admin_login.php');
require_valid_csrf('../admin_login.php');
include '../../database.php';

$username = clean_string($_POST['username'] ?? '');
$password = (string) ($_POST['password'] ?? '');

$stmt = $conn->prepare('SELECT id, username, password FROM admin WHERE username = ? LIMIT 1');
if (!$stmt) {
    header('Location: ../admin_login.php?error=' . urlencode('Unable to sign in right now.'));
    exit;
}
$stmt->bind_param('s', $username);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($row && verify_password_compatible($conn, 'admin', (int) $row['id'], $password, $row['password'])) {
    regenerate_authenticated_session();
    $_SESSION['id'] = (int) $row['id'];
    $_SESSION['user_name'] = $row['username'];
    $_SESSION['Adminloggedin'] = true;
    header('Location: ../admin_dashboard.php');
    exit;
}

header('Location: ../admin_login.php?error=' . urlencode('Invalid credentials'));
exit;
