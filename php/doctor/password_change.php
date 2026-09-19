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
    
    <title>Create Password</title>
</head>

<body>
    <div class="container">
    <a class="auth-brand" href="../index.php" aria-label="SmartCare Hub home"><img src="../../img/logo.jpg" alt="SmartCare Hub"></a>
        <form action="query/password.php" method="post">
      <?= csrf_field() ?>
            <div class="inputbox">
                <label for="newPassword">New Password:</label>
                <input type="password" id="newPassword" placeholder="Enter your new password"  name="newPassword" required>
            </div>
            <div class="inputbox">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
            </div>
            <div class="submit">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
  <script src="../../js/system.js" defer></script>
</body>
</html>