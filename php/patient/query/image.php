
<?php 
// Get user ID (assuming it's stored in session or session_values.php)
$userid = $_SESSION['id'];

// Define the upload directory and userid
$uploadDir = 'uploads/';
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