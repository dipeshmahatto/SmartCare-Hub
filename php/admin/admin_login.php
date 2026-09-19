<?php
require_once '../security.php';
secure_session_start();
?>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="../../css/login.css"/>
  <script src="../../js/admin_login_validate.js"></script>
  <script src="../../js/error.js"></script>
</head>
<body>
  <div class="container">
    <a class="auth-brand" href="../index.php" aria-label="SmartCare Hub home"><img src="../../img/logo.jpg" alt="SmartCare Hub"></a>
    <h1>Admin</h1>
    <form  onsubmit="return validateForm()" action="query/admin_login_process.php" method="post">
      <?= csrf_field() ?>
      <div class="login_box">
        <div class="input">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter Username" />
        </div>
        <div class="input">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter Password" />
        </div>
        <span id="error-message" style="color: red;"></span>
      </div>
      <div class="submit">
        <input type="submit" value="Login">
      </div>
      <div class="home">
        <a href="../index.php">Home</a>
      </div>
    </form>
  </div>
  <script src="../../js/system.js" defer></script>
</body>

</html>