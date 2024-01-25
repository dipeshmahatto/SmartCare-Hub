<?php
session_start();
include "../database.php";
if (isset($_POST['phoneNumber']) && isset($_POST['password'])) {
    $phoneNumber = $_POST['phoneNumber'];
    $password = $_POST['password'];
}
$sql = "SELECT * FROM patient WHERE phoneNumber='$phoneNumber' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['id'] = $row['id']; 
    $_SESSION['fullName'] = $row['fullName']; 
    $_SESSION['address'] = $row['address']; 
    $_SESSION['age'] = $row['age']; 
    $_SESSION['gender'] = $row['gender']; 
    $_SESSION['phoneNumber'] = $row['phoneNumber'];
    header('Location:patient_dashboard.php');
} else {
    header("Location: patient_login.php?error=Phone Number and Password Does Not Match");
    exit();
}
?>