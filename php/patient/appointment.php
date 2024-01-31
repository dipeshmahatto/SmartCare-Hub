<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: patient_login.php");
    exit;
}
include("session_values.php");
include "../database.php";
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_dashboard.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                <a href="patient_dashboard.php"><i class="fa-solid fa-user-doctor"></i>Active Appointment</a>
                <a href="#" class="active"><i class="fa-solid fa-user-doctor"></i>Book Appointment</a>
                <a href="history.php"><i class="fa-solid fa-clock-rotate-left"></i>History Appointment</a>
            </div>
        </div>
        <div class="content">
            <?php include("top.php") ?>
            <div class="form">
                <form action="appointment_process.php" method="post">
                    <div class="appointment">
                        <div class="input">
                            <label for="category">Select category :</label>
                            <select id="category" name="category">
                                <option value="" disabled selected>Select a category</option>
                                <option value="Surgery">Surgery</option>
                                <option value="Dental">Dental</option>
                                <option value="Ophthalmology">Ophthalmology</option>
                                <option value="Radiology">Radiology</option>

                            </select>
                        </div>
                        <div class="input">
                            <label for="doctor">Select doctor :</label>
                            <select id="doctor" name="doctor">
                            </select>
                        </div>
                        <div class="input">
                            <label for="day">Select day :</label>
                            <select id="day" name="day">
                                <option value='' disabled selected>Select a day</option>
                                <option value='SUNDAY'>SUNDAY</option>
                                <option value='MONDAY'>MONDAY</option>
                                <option value='TUESDAY'>TUESDAY</option>
                                <option value='WEDNESDAY'>WEDNESDAY</option>
                                <option value='THURSDAY'>THURSDAY</option>
                                <option value='FRIDAY'>FRIDAY</option>
                            </select>
                        </div>
                        <div class="input">
                            <label for="time">Select time :</label>
                            <select id="time" name="time">
                            </select>
                        </div>

                    </div>
                    <div class="submit">
                        <input type="submit" value="Book">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    $(document).ready(function () {
        $('#category').change(function () {
            var selectedCategory = $(this).val();
            $.ajax({
                url: 'get_doctors.php',
                type: 'POST',
                data: { category: selectedCategory },
                success: function (response) {
                    $('#doctor').html(response);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#day').change(function () {
            var selectedDay = $(this).val();
            var selectedDoctor = $('#doctor').val();
            $.ajax({
                url: 'get_times.php',
                type: 'POST',
                data: { doctor: selectedDoctor, day: selectedDay },
                success: function (response) {
                    $('#time').html(response);
                }
            });
        });
    });
</script>