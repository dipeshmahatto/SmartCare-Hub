<?php
require_once '../../security.php';
secure_session_start();
require_post_request('../patient_registration.php');
require_valid_csrf('../patient_registration.php');
include '../../database.php';

$fullName = clean_string($_POST['fullName'] ?? '');
$email = clean_string($_POST['email'] ?? '');
$phoneNumber = clean_string($_POST['phoneNumber'] ?? '');
$age = (int) ($_POST['age'] ?? 0);
$birthYear = (int) ($_POST['birthYear'] ?? 0);
$address = clean_string($_POST['address'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirmPassword'] ?? '');
$gender = strtoupper(clean_string($_POST['gender'] ?? ''));
$currentYear = (int) date('Y');

$redirectError = static function (string $message): never {
    header('Location: ../patient_registration.php?error=' . urlencode($message));
    exit;
};

if ($fullName === '' || !preg_match("/^[\p{L} .'-]+$/u", $fullName)) $redirectError('Enter a valid full name.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $redirectError('Enter a valid email address.');
if (!preg_match('/^(98|97|96)\d{8}$/', $phoneNumber)) $redirectError('Enter a valid 10-digit Nepal mobile number.');
if ($age < 1 || $age > 99) $redirectError('Age must be between 1 and 99.');
if ($birthYear < ($currentYear - 110) || $birthYear > $currentYear) $redirectError('Enter a valid birth year.');
if ($address === '') $redirectError('Address is required.');
if (!in_array($gender, ['M', 'F', 'O'], true)) $redirectError('Choose a valid gender.');
if (strlen($password) < 8) $redirectError('Password must be at least 8 characters long.');
if ($password !== $confirmPassword) $redirectError('Passwords do not match.');

$check = $conn->prepare('SELECT id FROM patient WHERE phoneNumber = ? OR email = ? LIMIT 1');
$check->bind_param('ss', $phoneNumber, $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    $check->close();
    $redirectError('That phone number or email is already registered.');
}
$check->close();

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO patient (fullName, email, phoneNumber, age, birthYear, address, password, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
if (!$stmt) $redirectError('Unable to create the account right now.');
$stmt->bind_param('sssiisss', $fullName, $email, $phoneNumber, $age, $birthYear, $address, $passwordHash, $gender);
$saved = $stmt->execute();
$stmt->close();

if ($saved) {
    header('Location: ../patient_login.php?registered=1');
    exit;
}
$redirectError('Unable to create the account. Please try again.');
