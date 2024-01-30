<?php
session_start();
include("session_values.php");
include "../database.php";
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_dashboard.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"> -->
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


                <!-- now call the function that will check the time is reserved or not  -->

                <!-- if not then make appointment successfull and insert the data in appointment table database -->



                <form action="appointment_process.php" method="post">
                    <div class="appointment">
                        <div class="input">
                            <label for="category">Select category :</label>
                            <select id="category" name="category">
                                <option value=""></option>
                                <option value="Surgery">Surgery</option>
                                <option value="Dental">Dental</option>
                                <option value="Ophthalmology">Ophthalmology</option>
                                <option value="Radiology">Radiology</option>

                            </select>
                        </div>
                        <div class="input">
                            <label for="doctor">Select doctor :</label>
                            <select id="doctor" name="doctor">
                                <option value=''>Select a category first</option>
                            </select>
                        </div>
                        <div class="input">
                            <label for="day">Select day :</label>
                            <select id="day" name="day">
                                <option value=''></option>
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
                                <?php
                                $sql = "SELECT times FROM times";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    // Output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['times'] . "'>" . $row['times'] . "</option>";
                                    }
                                }
                                ?>
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