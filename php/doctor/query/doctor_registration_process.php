<?php
session_start();
include "../../database.php";
if (
    isset($_POST['fullName']) && isset($_POST['email']) && isset($_POST['phoneNumber']) && isset($_POST['age'])
    && isset($_POST['birthYear']) && isset($_POST['address']) && isset($_POST['speciality'])
    && isset($_POST['qualification']) && isset($_POST['password']) && isset($_POST['confirmPassword'])
    && isset($_POST['gender'])
) {

    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $age = $_POST['age'];
    $birthYear = $_POST['birthYear'];
    $address = $_POST['address'];
    $speciality = $_POST['speciality'];
    $qualification = $_POST['qualification'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $gender = $_POST['gender'];
}
// phone number pattern 
$pattern = '/^(98|97|96)\d{8}$/';
// email pattern 
$emailpattern = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/';

if (empty($fullName)) {
    header("Location: ../doctor_registration.php?error=UserName is Required");
    exit();
}

//phone number validation
if (empty($phoneNumber)) {
    header("Location: ../doctor_registration.php?error=Phone Number is Required");
    exit();
} elseif (strlen($phoneNumber) != 10 || !preg_match($pattern, $phoneNumber)) {
    header("Location: ../doctor_registration.php?error=Phone Number is Invalid");
    exit();
} 
// else {
//     $phoneNumberCheck = "SELECT phoneNumber from doctor where phoneNumber='$phoneNumber'";
//     if (mysqli_query($conn, $phoneNumberCheck)) {
//         echo "<script>alert('This Phone Number is Already in use');</script>";
//         echo "<script>window.location.href = '../doctor_registration.php';</script>";
//         exit();
//     }
// }

// age validation
if (empty($age)) {
    header("Location: ../doctor_registration.php?error=Age is Required");
    exit();
} elseif ($age < 25 && $age > 100) {
    header("Location: ../doctor_registration.php?error=Age must be greater than 0 and less than 100");
    exit();
}

// Birth year validation
if (empty($birthYear)) {
    header("Location: ../doctor_registration.php?error=BirthYear is Required");
    exit();
} elseif ($birthYear < 1925 && $birthYear > 2000) {
    header("Location: ../doctor_registration.php?error=BirthYear is Invalid");
    exit();
}

if (empty($address)) {
    header("Location: ../doctor_registration.php?error=Address is Required");
    exit();
}
if (empty($gender)) {
    header("Location: ../doctor_registration.php?error=Gender is Required");
    exit();
}

if (empty($password)) {
    header("Location: ../doctor_registration.php?error=Password is Required");
    exit();
} else if (empty($confirmPassword)) {
    header("Location: ../doctor_registration.php?error=Password Confirmation is Required");
    exit();
} else if (strlen($password) < 8) {
    header("Location:../doctor_registration.php?error=Password must be at least 8 character");
    exit();
} else if ($password != $confirmPassword) {
    header("Location: ../doctor_registration.php?error=Password and Password Confirmation Does Not Match");
    exit();
}
$sql = "INSERT INTO doctor_approval(fullName,email,phoneNumber,age,birthYear,address,speciality,qualification,
password,gender)values('$fullName','$email','$phoneNumber','$age','$birthYear','$address','$speciality','$qualification','$password','$gender')";
if (mysqli_query($conn, $sql)) {
    header("Location:../../index.php");
}