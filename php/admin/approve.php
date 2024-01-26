<?php
include "../database.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
}
$sql = "SELECT * FROM doctor_approval where id='$id'";
$result = mysqli_query($conn, $sql);
while ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $fullName = $row["fullName"];
    $email = $row["email"];
    $phoneNumber = $row["phoneNumber"];
    $age = $row["age"];
    $birthYear = $row["birthYear"];
    $address = $row["address"];
    $speciality = $row["speciality"];
    $qualification = $row["qualification"];
    $password = $row["password"];
    $gender = $row["gender"];
}
if (isset($_POST["approve"])) {
    $sql = "INSERT INTO doctor(id,fullName,email,phoneNumber,age,birthYear,address,speciality,qualification,
        password,gender)values('$id','$fullName','$email','$phoneNumber','$age','$birthYear','$address','$speciality','$qualification','$password','$gender')";
        $sqll = "DELETE FROM doctor_approval WHERE id = $id";
} elseif (isset($_POST["reject"])) {
    // Perform actions for rejecting the user with the given ID
    $sql = "DELETE FROM doctor_approval WHERE id = $id";
}

if ($conn->query($sql) === TRUE&& $conn->query($sqll) === TRUE) {
    header("Location: approval.php");
}
$conn->close();
?>
