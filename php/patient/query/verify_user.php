<?php 

include "../../database.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phoneNumber = $_POST["phoneNumber"];
    $birthYear = $_POST["birthYear"];
}

$sql = "SELECT * FROM patient WHERE phoneNumber='$phoneNumber' AND birthYear='$birthYear'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['oldpassword'] = $row['password'];
    $_SESSION['forgotpasswordphonenumber']=$phoneNumber;
    header('Location:../password_change.php');
} else {
    echo '<script>
            alert("Phone Number and BirthYear Does Not Match");
            window.location.href = "../forgot.php";
          </script>';
    exit();
}
?>
