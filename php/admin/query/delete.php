<?php
include "../../database.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $DoctorfullName = $_POST["fullName"];
}
$sql = "DELETE from doctor WHERE id = '$id'";
$result = mysqli_query($conn, $sql);

$appointment ="DELETE from appointment where doctor='$DoctorfullName'";
$result2 =mysqli_query($conn,$appointment) ;


if (($conn->query($sql) === TRUE) || ($conn->query($appointment)==TRUE)){
    header("Location: ../doctor_list.php");
}
$conn->close();
?>