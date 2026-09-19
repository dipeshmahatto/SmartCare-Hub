<?php
require_once '../../security.php';
secure_session_start();
require_post_request('../doctor_registration.php');
require_valid_csrf('../doctor_registration.php');
include '../../database.php';

$fullName = clean_string($_POST['fullName'] ?? '');
$email = clean_string($_POST['email'] ?? '');
$phoneNumber = clean_string($_POST['phoneNumber'] ?? '');
$age = (int) ($_POST['age'] ?? 0);
$birthYear = (int) ($_POST['birthYear'] ?? 0);
$address = clean_string($_POST['address'] ?? '');
$speciality = clean_string($_POST['speciality'] ?? '');
$qualification = clean_string($_POST['qualification'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirmPassword'] ?? '');
$gender = strtoupper(clean_string($_POST['gender'] ?? ''));
$currentYear = (int) date('Y');
$allowedSpecialities = ['Surgery', 'Dental', 'Ophthalmology', 'Radiology', 'Gynoclogist'];
$allowedQualifications = ['MBBS', 'MD', 'PHD', 'BDS'];

$redirectError = static function (string $message): never {
    header('Location: ../doctor_registration.php?error=' . urlencode($message));
    exit;
};

if ($fullName === '' || !preg_match("/^[\p{L} .'-]+$/u", $fullName)) $redirectError('Enter a valid full name.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $redirectError('Enter a valid email address.');
if (!preg_match('/^(98|97|96)\d{8}$/', $phoneNumber)) $redirectError('Enter a valid 10-digit Nepal mobile number.');
if ($age < 21 || $age > 100) $redirectError('Enter a valid age.');
if ($birthYear < ($currentYear - 110) || $birthYear > $currentYear) $redirectError('Enter a valid birth year.');
if ($address === '') $redirectError('Address is required.');
if (!in_array($speciality, $allowedSpecialities, true)) $redirectError('Choose a valid speciality.');
if (!in_array($qualification, $allowedQualifications, true)) $redirectError('Choose a valid qualification.');
if (!in_array($gender, ['M', 'F', 'O'], true)) $redirectError('Choose a valid gender.');
if (strlen($password) < 8) $redirectError('Password must be at least 8 characters long.');
if ($password !== $confirmPassword) $redirectError('Passwords do not match.');

$check = $conn->prepare('SELECT phoneNumber FROM doctor WHERE phoneNumber = ? UNION SELECT phoneNumber FROM doctor_approval WHERE phoneNumber = ? LIMIT 1');
$check->bind_param('ss', $phoneNumber, $phoneNumber);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    $check->close();
    $redirectError('That phone number already has an account or pending application.');
}
$check->close();

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO doctor_approval (fullName, email, phoneNumber, age, birthYear, address, speciality, qualification, password, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
if (!$stmt) $redirectError('Unable to submit the application right now.');
$stmt->bind_param('sssiisssss', $fullName, $email, $phoneNumber, $age, $birthYear, $address, $speciality, $qualification, $passwordHash, $gender);
$saved = $stmt->execute();
$stmt->close();

if ($saved) {
    header('Location: ../../index.php?doctor_application=sent');
    exit;
}
$redirectError('Unable to submit the application. Please try again.');
