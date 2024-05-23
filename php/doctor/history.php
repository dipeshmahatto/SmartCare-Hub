<?php session_start();
include("query/session.php");
include("../database.php");
include("query/image.php");
// Checking if the user is logged in
if (!isset($_SESSION['doctorloggedin']) || $_SESSION['doctorloggedin'] !== true) {
    header("Location: doctor_login.php");
    exit;
}
$app = "SELECT * FROM appointment WHERE doctor='$DoctorfullName' AND status=1";
$result = mysqli_query($conn, $app);
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/doctor_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Doctor Dashboard</title>
</head>

<body>
    <div class="container">
        <div class="nav">
            <div class="profile">
            <img src="<?= $imagePath ?>" alt="Profile Image not found">
                <br>
                <h3><?php echo $DoctorfullName ?></h3>
            </div>
            <hr>
            <div class="operation">
                <a href="doctor_dashboard.php"><i class="fa-solid fa-user-doctor"></i>Active Appointment</a>
                <a href="#" class="history"><i class="fa-solid fa-clock-rotate-left"></i>History Appointment</a>
            </div>
        </div>
        <div class="content">
            <?php include("top.php") ?>

            <div class="table">
                <table>
                    <tr>
                        <th>Appointment Id</th>
                        <th>Patient Name</th>
                        <th>Category</th>
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
                                <?php
                                $pid = $row['pid']; 
                                $patientQuery = "SELECT fullName FROM patient WHERE id='$pid'";
                                $patientResult = mysqli_query($conn, $patientQuery);

                                if ($patientResult) {
                                    $roww = mysqli_fetch_assoc($patientResult); 
                                    $fullName = $roww['fullName']; // Geting the full name
                                    echo $fullName;
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo $row['category']; ?>
                            </td>
                            <td>
                                <?php echo $row["app_time"] ?>
                            </td>
                            <td>
                                <?php echo $row["day"]; ?>
                            </td>
                            <td>
                                <?php echo "Completed"; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>