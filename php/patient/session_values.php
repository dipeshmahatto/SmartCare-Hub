<?php
session_start();

// Access session data from patient login process 1
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
}
if (isset($_SESSION['fullName'])) {
    $fullName = $_SESSION['fullName'];
}
if (isset($_SESSION['address'])) {
    $address = $_SESSION['address'];
}
if (isset($_SESSION['age'])) {
    $age = $_SESSION['age'];
}
if (isset($_SESSION['gender'])) {
    $gender = $_SESSION['gender'];
}
if (isset($_SESSION['phoneNumber'])) {
    $phoneNumber = $_SESSION['phoneNumber'];
}

?>