<?php
session_start();
include "../../database.php";
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
}
$sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['id'] = $row['id'];
    $_SESSION['user_name'] = $row['user_name'];
    $_SESSION['Adminloggedin'] = true;
    header('Location:../admin_dashboard.php');
} else {
    header("Location: ../admin_login.php?error=" . urlencode("Invalid credentials"));
    exit();
}
?>