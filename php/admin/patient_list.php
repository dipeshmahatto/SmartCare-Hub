<?php
include "../database.php";
$sql = "SELECT * FROM patient";
$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="container">
        <div class="nav">
            <div class="profile">
                <img src="../../img/admin_profile.jpg" alt="">
                <br>
                <h3>Admin</h3>
            </div>
            <hr>
            <div class="operation">
                <a href="admin_dashboard.php"><i class="fa-solid fa-gauge-high"></i>Dashboard</a>
                <a href="doctor_list.php"><i class="fa-solid fa-user-doctor"></i>Doctor</a>
                <a href="patient_list.php" class="patient"><i class="fa-solid fa-bed-pulse"></i>Patient</a>
                <a href="approval.php"><i class="fa-solid fa-person-circle-check"></i> Approvel</a>
            </div>
        </div>
        <div class="content">
            <?php include("top.php") ?>
            <div class="table">
                <table>
                    <tr>
                        <th>Patient Id</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>phone number</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= $row["id"] ?>
                            </td>
                            <td>
                                <?= $row["fullName"] ?>
                            </td>
                            <td>
                                <?= $row["address"] ?>
                            </td>
                            <td>
                                <?= $row["age"] ?>
                            </td>
                            <td>
                                <?= $row["gender"] ?>
                            </td>
                            <td>
                                <?= $row["phoneNumber"] ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>


                </table>
            </div>
        </div>
    </div>
</body>

</html>