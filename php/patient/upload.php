<?php
session_start();
include("../database.php");

// Simulate logged-in user full name
$userFullName = str_replace(' ', '_', $_SESSION['fullName']); // Replace spaces with underscores

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $newFileName = $userFullName . '.' . $fileExtension;
    $uploadFile = $uploadDir . basename($newFileName);

    // Ensure the uploads directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        header("Location: patient_dashboard.php");
    } else {
        echo "<div style='text-align: center; margin-top: 50px;'>";
        echo "<h2>Possible file upload attack!</h2>";
        echo "<a href='patient_dashboard.php'>Try again</a>";
        echo "</div>";
    }
} else {
    echo "<div style='text-align: center; margin-top: 50px;'>";
    echo "<h2>No file uploaded or there was an upload error.</h2>";
    echo "<a href='patient_dashboard.php'>Try again</a>";
    echo "</div>";
}
?>
