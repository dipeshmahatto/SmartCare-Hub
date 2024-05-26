
<html>
<head>
  <meta charset="utf-8" />
  <title>Patient Registration</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <link rel="stylesheet" href="../../css/registration.css" />
  <script src="../../js/error.js"></script>
</head>

<body>
  <div class="container">
    <h1>Patient</h1>
    <form action="query/patient_registration_process.php" novalidate method="post">
      <div class="register_box">
        <!-- Full name  -->
        <div class="input">
          <label for="fullName">Full Name</label>
          <input type="text" id="fullName" name="fullName" placeholder="Enter Full Name"  />
        </div>
        <!-- Email  -->
        <div class="input">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter Email"  />
        </div>
        <!-- Phone number  -->
        <div class="input">
          <label for="phoneNumber">Phone Number</label>
          <input type="number" id="phoneNumber" name="phoneNumber" placeholder="Enter Phone Number"  />
        </div>
        <!-- Age  -->
        <div class="input">
          <label for="age">Age</label>
          <input type="number" id="age" name="age" placeholder="Enter Age"  />
        </div>
        <!-- Birth Year  -->
        <div class="input">
          <label for="birthYear">Birth Year</label>
          <input type="number" id="birthYear" name="birthYear" placeholder="Enter Birth Year Eg:- 1997"  />
        </div>
        <div class="input">
          <label for="address">Address</label>
          <input type="text" id="address" name="address" placeholder="Enter Address"  />
        </div>
        <!-- Password  -->
        <div class="input">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter Password"  />
        </div>
        <div class="input">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password"  />
        </div>
      </div>
      <!-- Gender  -->
      <span class="gender">Gender</span>
      <div class="genders">
        <input type="radio" name="gender" id="male" value="M" >
        <label for="male">Male</label>
        <input type="radio" name="gender" id="female" value="F">
        <label for="female">Female</label>
        <input type="radio" name="gender" id="other" value="O">
        <label for="other">Other</label>
      </div>
      <span id="error-message" style="color: red;"></span>

      <div class="submit">
        <input type="submit" value="Register">
      </div>
    </form>
    <div class="already">
      <label for="login">Already have an account: <a href="patient_login.php">Login</a></label>
    </div>
  </div>
</body>

</html>