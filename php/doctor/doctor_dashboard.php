<?php session_start();
include("query/session.php");
include("../database.php");
// Checking if the user is logged in
if (!isset($_SESSION['doctorloggedin']) || $_SESSION['doctorloggedin'] !== true) {
    header("Location: doctor_login.php");
    exit;
}


include("query/image.php");

$app = "SELECT * FROM appointment WHERE doctor='$DoctorfullName' AND status=0";
$result = mysqli_query($conn, $app);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/doctor_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Doctor Dashboard</title>
    <style>
        <?php if ($imagePath) : ?>
        #upload-container {
            display: none;
        }
        <?php endif; ?>
    </style>
</head>

<body>
    <div class="container">
        <div class="nav">
        <div class="profile">
                <!-- Profile img are shown using database -->
                <img src="<?= $imagePath ?>" alt="Profile Image not found">
                <br>
                <h3><?= $DoctorfullName; ?></h3>
                <div class="upload-container" id="upload-container">
                    <form action="upload.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="file" id="file" accept="image/*">
                        <br>
                        <input type="submit" value="Upload Image">
                    </form>
                </div>
            </div>
            <hr>
            <div class="operation">
                <a href="#" class="active"><i class="fa-solid fa-user-doctor"></i>Active Appointment</a>
                <a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i>History Appointment</a>
            </div>
        </div>
        <div class="content">
            <?php include("top.php") ?>

            <div class="table">
                <table>
                    <tr>
                        <th>Appointment Id</th>
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
                                <?php echo $row["aid"]; ?>.
                            </td>
                            <td>
                                <?php echo $row["category"]; ?>
                            </td>
                            <td>
                                <?php echo $row["app_time"] ?>
                            </td>
                            <td>
                                <?php echo $row["day"]; ?>
                            </td>
                            <td>
                                <form method="post" action="query/completed.php">
                                    <input type="hidden" name="id" value="<?= $row["aid"] ?>">
                                    <button type="submit" name="completed" class="completed">completed</button>
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