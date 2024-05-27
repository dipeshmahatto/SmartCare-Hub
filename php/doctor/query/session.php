<?php
// Access session data from patient login process 1
if (isset($_SESSION['id'])) {
    $id = $_SESSION['did'];
}
if (isset($_SESSION['DoctorfullName'])) {
    $DoctorfullName = $_SESSION['DoctorfullName'];
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