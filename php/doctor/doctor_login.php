<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Doctor Login</title>
  <link rel="stylesheet" href="../../css/login.css" />
  <script src="../../js/user_login_validate.js"></script>
</head>

<body>
  <div class="container">
    <h1>Doctor</h1>
    <form onsubmit="validateForm()" action="doctor_login_process.php" method="post">
      <div class="login_box">
        <div class="input">
          <label for="phoneNumber">Phone Number</label>
          <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Enter Phone Number"  />
        </div>
        <div class="input">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter Password"  />
        </div>
      </div>
      <div class="forgot">
        <a href="#">Forgot Password ?</a>
      </div>
      <div class="submit">
        <input type="submit" value="Login">
      </div>
    </form>
    <div class="register">
      <label for="login">Don`t have an account: </label><a href="doctor_registration.php">Register</a>
    </div>
  </div>
</body>

</html>