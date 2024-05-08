<?php
include "database.php";
$sql = "SELECT * FROM doctor";
$result = mysqli_query($conn, $sql);
?>
<html lang="en">

This is sample of landing page.

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>SmartCare Hub</title>
</head>

<body>
    <div class="container">
        <div class="doctors">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="list" id="doc">
                    <img src="../img/admin_profile.jpg" alt="Image Not Found">

                    <h3>
                        <?php echo "Name :" . " " . $row['fullName']; ?>
                    </h3>
                    <p>
                        <?php echo "Speciality :" . " " . $row['speciality']; ?>
                    <p>
                    <p>
                        <?php echo "Qualification :" . " " . $row['qualification']; ?>
                    </p>
                    <p>
                        <?php echo "Email :" . " " . $row['email']; ?>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>

</html>

<?php
include("footer.php");
?>