<?php
require_once '../security.php';
secure_session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/forgot.css">
    <title>Forgot password</title>
</head>

<body>
    <div class="container">
    <a class="auth-brand" href="../index.php" aria-label="SmartCare Hub home"><img src="../../img/logo.jpg" alt="SmartCare Hub"></a>
        <form action="query/verify_user.php" method="post">
      <?= csrf_field() ?>
            <div class="inputbox">
                <label for="phoneNumber">Mobile Number :</label>
                <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Enter your registred number" maxlength="10" required>
            </div>
            <div class="inputbox">
                <label for="birthYear">Birth Year:</label>
                <input type="text" id="birthYear" name="birthYear" placeholder="Enter your birth Year" required>
            </div>
            <div class="submit">
                <input type="submit" value="Submit">
            </div>
        </form>
        <div class="home">
            <a href="../index.php">Home</a>
        </div>
    </div>
  <script src="../../js/system.js" defer></script>
</body>

</html>