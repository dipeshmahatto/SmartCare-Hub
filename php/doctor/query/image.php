<?php
include_once('query/session.php');
include_once('../database.php');

$userid = isset($_SESSION['did']) ? (int) $_SESSION['did'] : 0;
$uploadDir = 'uploads/';
$imagePath = '';

foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
    $filePath = $uploadDir . $userid . '.' . $ext;
    if (file_exists($filePath)) {
        $imagePath = $filePath;
        break;
    }
}
?>
