<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: patient_login.php");
    exit;
}
include("session_values.php");
include("../database.php");


// active appointments
$app = "SELECT * FROM appointment WHERE pid='$id' AND status=0";
$result = mysqli_query($conn, $app);
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
                        <th>Category</th>
                        <th>Doctor Name</th>
                        <th>Appointment time</th>
                        <th>Day</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <?php echo $row["aid"]; ?>
                        </td>
                        <td>
                            <?php echo $row["category"]; ?>
                        </td>
                        <td>
                            <?php echo $row["doctor"]; ?>
                        </td>
                        <td>
                            <?php echo $row["app_time"] ?>
                        </td>
                        <td>
                            <?php echo $row["day"]; ?>
                        </td>
                        <td>
                            <?php echo "x"; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>