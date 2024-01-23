<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/approval.css">
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
                <a href="patient_list.php"><i class="fa-solid fa-bed-pulse"></i>Patient</a>
                <a href="approval.php" class="approval"><i class="fa-solid fa-person-circle-check"></i> Approval</a>
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
                        <th>operation</th>

                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>