<?php 
include "../database.php";
//counting doctors from database
$sql = "SELECT COUNT(*) AS doctors FROM doctor";
$count1 = mysqli_query($conn, $sql);
$doctors = $count1->fetch_assoc()['doctors'];
// counting patients from database 
$sql = "SELECT COUNT(*) AS patient FROM patient";
$count2 = mysqli_query($conn, $sql);
$patients = $count2->fetch_assoc()['patient'];
// counting doctors registration to be verifed by admin from database 
$sql = "SELECT COUNT(*) AS approve FROM doctor_approval";
$count3 = mysqli_query($conn, $sql);
$approvals = $count3->fetch_assoc()['approve'];