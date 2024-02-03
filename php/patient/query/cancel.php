<?php
include "../../database.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
}
$sql = "DELETE from appointment WHERE aid = '$id'";
$result = mysqli_query($conn, $sql);
if ($conn->query($sql) === TRUE) {
    header("Location: ../patient_dashboard.php");
}
$conn->close();
?>
