<?php
include ("query/session.php");
include ("../database.php");
$userid = $_SESSION['did'];
// Define the upload directory and userid
$uploadDir = 'uploads/';

// Check for existing profile image
$imagePath = '';
$extensions = ['jpg', 'jpeg'];
foreach ($extensions as $ext) {
    $filePath = $uploadDir . $userid . '.' . $ext;
    if (file_exists($filePath)) {
        $imagePath = $filePath;
        echo "<style>
        #uploadimg{display:none;}
        </style>";
        break;
    }
}