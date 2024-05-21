<?php
session_start();
include "query/counts.php";
if (!isset($_SESSION['Adminloggedin']) || $_SESSION['Adminloggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}
$sql = "SELECT * FROM doctor";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css//admin_dashboard.css">
    <script src="../../js/validates.js"></script>
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
                <a href="doctor_list.php" class="doctor"><i class="fa-solid fa-user-doctor"></i>Doctor</a>
                <a href="patient_list.php"><i class="fa-solid fa-bed-pulse"></i>Patient</a>
                <a href="approval.php"><i class="fa-solid fa-person-circle-check"></i> Approval (
                    <?php echo $approvals; ?>)
                </a>
            </div>
        </div>
        <div class="content">
            <?php include("top.php") ?>
            <div class="table">
                <table>
                    <tr>
                        <th>Doctor Id</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>phone number</th>
                        <th>Qualification</th>
                        <th>Speciality</th>
                        <th>Operation</th>
                    </tr>

                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= $row["id"] ?>.
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
                            <td>
                                <?= $row["qualification"] ?>
                            </td>
                            <td>
                                <?= $row["speciality"] ?>
                            </td>
                            <td>
                                <form action="query/delete.php" method="post">
                                    <input type="hidden" name="id" value="<?= $row["id"] ?>">
                                    <input type="hidden" name="fullName" value="<?= $row["fullName"]?>">
                                    <button type="submit" name="delete" class="delete" onclick="Remove()">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </table>
            </div>
        </div>
    </div>
</body>

</html>