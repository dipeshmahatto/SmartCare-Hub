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

    // Phone number pattern
    $pattern = '/^(98|97|96)\d{8}$/';
    // Email pattern
    $emailPattern = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/';
    // Name pattern (letters and spaces only)
    $namePattern = '/^[a-zA-Z\s]+$/';

    // Validate Full Name
    if (empty($fullName) || !preg_match($namePattern, $fullName)) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Invalid Full Name"));
        exit();
    }

    // Validate Email
    if (empty($email) || !preg_match($emailPattern, $email)) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Invalid Email"));
        exit();
    }

    // Validate Phone Number
    if (empty($phoneNumber) || !preg_match($pattern, $phoneNumber)) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Invalid Phone Number"));
        exit();
    }

    // Validate Age
    if (empty($age) || $age < 25 || $age > 100) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Invalid Age"));
        exit();
    }

    // Validate Birth Year
    if (empty($birthYear) || $birthYear < 1925 || $birthYear > 2000) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Invalid Birth Year"));
        exit();
    }

    // Validate Address
    if (empty($address)) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Address is Required"));
        exit();
    }

    // Validate Gender
    if (empty($gender)) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Gender is Required"));
        exit();
    }

    // Validate Password
    if (empty($password) || strlen($password) < 8 || $password != $confirmPassword) {
        header("Location: ../doctor_registration.php?error=" . urlencode("Invalid Password"));
        exit();
    }

    // Insert Data into Database
    $sql = "INSERT INTO doctor_approval(fullName,email,phoneNumber,age,birthYear,address,speciality,qualification,password,gender) 
            VALUES ('$fullName','$email','$phoneNumber','$age','$birthYear','$address','$speciality','$qualification','$password','$gender')";

    if (mysqli_query($conn, $sql)) {
        header("Location:../../index.php");
        exit();
    } else {
        header("Location: ../doctor_registration.php?error=" . urlencode("Error: " . mysqli_error($conn)));
        exit();
    }
} else {
    header("Location: ../doctor_registration.php?error=" . urlencode("All fields are required"));
    exit();
}
?>