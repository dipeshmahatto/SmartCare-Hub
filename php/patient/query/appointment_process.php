<?php
session_start();
include "../../database.php";
include "session_values.php";
if (
    isset($_POST['category']) && isset($_POST['doctor']) && isset($_POST['time']) && isset($_POST['day'])
) {
    $pid = $id;
    $category = $_POST['category'];
    $doctor = $_POST['doctor'];
    $time = $_POST['time'];
    $day = $_POST['day'];
}

$sql = "INSERT INTO appointment(pid,category,doctor,app_time,day)values('$pid','$category','$doctor','$time','$day')";
$result = mysqli_query($conn, $sql);

if ($result == 1) {
    header('Location:../patient_dashboard.php');
} else {
    header("Location: ../patient_login.php?error= Booking unsuccessfull");
    exit();
}