<?php 
include("query/session.php");
include("../database.php");
// Get user ID (assuming it's stored in session or session_values.php)
$userId = $_SESSION['id'];
// Define the upload directory and user full name
$uploadDir = 'uploads/';
$userid = str_replace(' ', '_', $_SESSION['id']); // Replace spaces with underscores

// Check for existing profile image
$imagePath = '';
$extensions = ['jpg', 'jpeg'];
foreach ($extensions as $ext) {
    $filePath = $uploadDir . $userid . '.' . $ext;
    if (file_exists($filePath)) {
        $imagePath = $filePath;
        break;
    }
}