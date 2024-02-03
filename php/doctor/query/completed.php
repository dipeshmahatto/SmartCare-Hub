<?php
include "../../database.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
}
$sql = "UPDATE appointment SET status = 1 WHERE aid = '$id'";
$result = mysqli_query($conn, $sql);
if ($conn->query($sql) === TRUE) {
    header("Location: ../doctor_dashboard.php");
}
$conn->close();
?>
