<?php
require_once '../security.php';
secure_session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: patient_login.php');
    exit;
}
require_post_request('patient_dashboard.php');
require_valid_csrf('patient_dashboard.php');

$userId = (int) ($_SESSION['id'] ?? 0);
$file = $_FILES['file'] ?? null;
$maxBytes = 2 * 1024 * 1024;
$allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

if ($userId <= 0 || !$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] <= 0 || $file['size'] > $maxBytes) {
    header('Location: patient_dashboard.php?error=' . urlencode('Choose a JPG, PNG or WebP image up to 2 MB.'));
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
if (!isset($allowedMimes[$mime])) {
    header('Location: patient_dashboard.php?error=' . urlencode('Unsupported profile image type.'));
    exit;
}

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
foreach (['jpg', 'jpeg', 'png', 'webp'] as $oldExt) {
    $old = $uploadDir . '/' . $userId . '.' . $oldExt;
    if (is_file($old)) @unlink($old);
}
$destination = $uploadDir . '/' . $userId . '.' . $allowedMimes[$mime];
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    header('Location: patient_dashboard.php?error=' . urlencode('Profile photo could not be saved.'));
    exit;
}
header('Location: patient_dashboard.php?photo=updated');
exit;
