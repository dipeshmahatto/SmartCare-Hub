<?php

include "../../database.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];
    $phoneNumber = $_SESSION['forgotpasswordphonenumber'];
    $oldpassword = $_SESSION['oldpassword'];
}

if ($newPassword !== $confirmPassword) {
    header("Location: ../password_change.php?error=Passwords do not match");
    exit();
} elseif (strlen($newPassword) < 8) {
    header("Location: ../password_change.php?error=Password must be at least 8 characters long");
    exit();
} elseif ($oldpassword === $newPassword) {
    echo '<script>
            alert("Cann`t use old password");
            window.location.href = "../forgot.php";
          </script>';
    exit();
}

$sql = "UPDATE patient SET password='$newPassword' WHERE phoneNumber='$phoneNumber'";
$result = mysqli_query($conn, $sql);

if ($result > 0) {
    session_unset();
    session_destroy();
    echo '<script>
            alert("Password changed successfully");
            window.location.href = "../patient_login.php";
          </script>';
} else {
    header("Location: ../forgot.php");
    exit();
}
?>