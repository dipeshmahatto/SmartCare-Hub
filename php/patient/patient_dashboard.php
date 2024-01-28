<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: patient_login.php");
    exit;
}
include("session_values.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Patient Dashboard</title>
</head>

<body>
    <div class="container">
        <div class="nav">
            <div class="profile">
                <img src="../../img/admin_profile.jpg" alt="">
                <br>
                <h3>
                    <?php echo $fullName; ?>
                </h3>
            </div>
            <hr>
            <div class="operation">
                <a href="#" class="active"><i class="fa-solid fa-user-doctor"></i>Active Appointment</a>
                <a href="appointment.php" c><i class="fa-solid fa-user-doctor"></i>Book Appointment</a>
                <a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i>History Appointment</a>
            </div>
        </div>
        <div class="content">
            <?php include("top.php") ?>

            <div class="table">
                <table>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>phone number</th>
                    </tr>
                    <tr>
                        <td>
                            <?php echo "x"; ?>
                        </td>
                        <td>
                            <?php echo "x"; ?>
                        </td>
                        <td>
                            <?php echo "x"; ?>
                        </td>
                        <td>
                            <?php echo "x"; ?>
                        </td>
                        <td>
                            <?php echo "x"; ?>
                        </td>
                        <td>
                            <?php echo "x"; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>