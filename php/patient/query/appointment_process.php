<?php
session_start();
include "../../database.php";
include "session_values.php";
if (
    !empty($_POST['category']) &&
    !empty($_POST['doctor']) &&
    !empty($_POST['time']) &&
    !empty($_POST['day'])
) {
    $pid = $id;
    $category = $_POST['category'];
    $doctor = $_POST['doctor'];
    $time = $_POST['time'];
    $day = $_POST['day'];


    $sql = "INSERT INTO appointment(pid,category,doctor,app_time,day)values('$pid','$category','$doctor','$time','$day')";
    $result = mysqli_query($conn, $sql);
    if ($result == 1) {
        header('Location:../patient_dashboard.php');
    } else {
        header("Location: ../patient_login.php?error= Booking unsuccessfull");
        exit();
    }
} else{
    header('Location:../appointment.php?error= Fill all the fields');
}
