<?php
session_start();
include "../../database.php";
if (isset($_POST['phoneNumber']) && isset($_POST['password'])) {
    $phoneNumber = $_POST['phoneNumber'];
    $password = $_POST['password'];
}
$sql = "SELECT * FROM doctor WHERE phoneNumber='$phoneNumber' AND password='$password'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['id'] = $row['id'];
    $_SESSION['DoctorfullName'] = $row['fullName'];
    $_SESSION['phoneNumber'] = $row['phoneNumber'];
    $_SESSION['doctorloggedin'] = true;
    header('Location:../doctor_dashboard.php');
} else {
    header("Location: ../doctor_login.php?error=Phone Number and Password Does Not Match");
    exit();
}
?>