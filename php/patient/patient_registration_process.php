<?php
session_start();
include "../database.php";
if (
    isset($_POST['fullName']) && isset($_POST['email']) && isset($_POST['phoneNumber']) && isset($_POST['age'])
    && isset($_POST['birthYear']) && isset($_POST['address']) && isset($_POST['password']) && isset($_POST['confirmPassword'])
    && isset($_POST['gender'])
) {

    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $age = $_POST['age'];
    $birthYear = $_POST['birthYear'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $gender = $_POST['gender'];
}
// phone number pattern 
$pattern = '/^(98|97|96)\d{8}$/';
$emailpattern = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/';

if (empty($fullName)) {
    header("Location: patient_registration.php?error=UserName is Required");
    exit();
}

//phone number validation
if (empty($phoneNumber)) {
    header("Location: patient_registration.php?error=Phone Number is Required");
    exit();
} elseif (strlen($phoneNumber) != 10 || !preg_match($pattern, $phoneNumber)) {
    header("Location: patient_registration.php?error=Phone Number is Invalid");
    exit();
}

// age validation
if (empty($age)) {
    header("Location: patient_registration.php?error=Age is Required");
    exit();
} elseif ($age < 0 && $age > 100) {
    header("Location: patient_registration.php?error=Age must be greater than 0 and less than 100");
    exit();
}

// Birth year validation
if (empty($birthYear)) {
    header("Location: patient_registration.php?error=BirthYear is Required");
    exit();
} elseif ($birthYear < 1925 && $age > 2024) {
    header("Location: patient_registration.php?error=BirthYear is Invalid");
    exit();
}

if (empty($address)) {
    header("Location: patient_registration.php?error=Address is Required");
    exit();
}
if (empty($gender)) {
    header("Location: patient_registration.php?error=Gender is Required");
    exit();
}

if (empty($password)) {
    header("Location: patient_registration.php?error=Password is Required");
    exit();
} else if (empty($confirmPassword)) {
    header("Location: patient_registration.php?error=Password Confirmation is Required");
    exit();
} else if (strlen($password) < 8) {
    header("Location:patient_registration.php?error=Password must be at least 8 character");
    exit();
} else if ($password != $confirmPassword) {
    header("Location: patient_registration.php?error=Password and Password Confirmation Does Not Match");
    exit();
}
$sql = "INSERT INTO patient(fullName,email,phoneNumber,age,birthYear,address,password,gender)values('$fullName','$email','$phoneNumber','$age','$birthYear','$address','$password','$gender')";
if (mysqli_query($conn, $sql)) {
    header("Location:patient_login.php?msg=register Successfully");
}