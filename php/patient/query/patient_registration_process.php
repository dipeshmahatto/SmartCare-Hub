<?php
session_start();
include "../../database.php";

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

    // Define patterns for validation
    $pattern = '/^(98|97|96)\d{8}$/'; // phone number pattern
    $emailPattern = '/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/'; // email pattern
    $namePattern = '/^[a-zA-Z\s]+$/'; // name pattern (letters and spaces only)

    // Validate full name
    if (empty($fullName)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Name is Required"));
        exit();
    } elseif (!preg_match($namePattern, $fullName)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Full Name cannot contain numbers or special characters"));
        exit();
    }

    // Validate email
    if (empty($email)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Email is Required"));
        exit();
    } elseif (!preg_match($emailPattern, $email)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Invalid Email Format"));
        exit();
    }

    // Validate phone number
    if (empty($phoneNumber)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Phone Number is Required"));
        exit();
    } elseif (strlen($phoneNumber) != 10 || !preg_match($pattern, $phoneNumber)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Phone Number is Invalid"));
        exit();
    } else {
        $phoneNumberCheckQuery = "SELECT phoneNumber FROM patient WHERE phoneNumber=?";
        $stmt = mysqli_prepare($conn, $phoneNumberCheckQuery);
        mysqli_stmt_bind_param($stmt, "s", $phoneNumber);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Phone number already exists in the database
            header("Location: ../patient_registration.php?error=" . urlencode("This Phone Number is Already in use"));
            exit();
        }

        mysqli_stmt_close($stmt);
    }

    // Age validation
    if (empty($age)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Age is Required"));
        exit();
    } elseif ($age <= 0 || $age >= 100) {
        header("Location: ../patient_registration.php?error=" . urlencode("Age must be between 1 and 99"));
        exit();
    }

    // Birth year validation
    if (empty($birthYear)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Birth Year is Required"));
        exit();
    } elseif ($birthYear < 1925 || $birthYear > 2024) {
        header("Location: ../patient_registration.php?error=" . urlencode("Birth Year is Invalid"));
        exit();
    }

    // Validate address and gender
    if (empty($address)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Address is Required"));
        exit();
    }
    if (empty($gender)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Gender is Required"));
        exit();
    }

    // Password validation
    if (empty($password)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Password is Required"));
        exit();
    } elseif (empty($confirmPassword)) {
        header("Location: ../patient_registration.php?error=" . urlencode("Password Confirmation is Required"));
        exit();
    } elseif (strlen($password) < 8) {
        header("Location: ../patient_registration.php?error=" . urlencode("Password must be at least 8 characters long"));
        exit();
    } elseif ($password != $confirmPassword) {
        header("Location: ../patient_registration.php?error=" . urlencode("Password and Password Confirmation Do Not Match"));
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO patient(fullName,email,phoneNumber,age,birthYear,address,password,gender) VALUES ('$fullName','$email','$phoneNumber','$age','$birthYear','$address','$password','$gender')";

    if (mysqli_query($conn, $sql)) {
        header("Location:../../index.php");
        exit();
    } else {
        header("Location: ../patient_registration.php?error=" . urlencode("Error: " . mysqli_error($conn)));
        exit();
    }
} else {
    // Handle case when form fields are not set
    header("Location: ../patient_registration.php?error=" . urlencode("All fields are required"));
    exit();
}
?>