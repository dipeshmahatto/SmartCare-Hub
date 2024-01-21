<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Patient Login</title>
  <link rel="stylesheet" href="../../css/login.css" />
</head>

<body>
  <div class="container">
    <h1>Patient</h1>
    <form action="#">
      <div class="login_box">
        <div class="input">
          <label for="phoneNumber">Phone Number</label>
          <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Enter Phone Number" required />
        </div>
        <div class="input">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter Password" required />
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
      <label for="login">Don`t have an account: </label><a href="patient_registration.php">Register</a>
    </div>
  </div>
</body>

</html>