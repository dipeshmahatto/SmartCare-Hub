<html>

<head>
  <meta charset="utf-8" />
  <title>Patient Registration</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <link rel="stylesheet" href="../../css/registration.css" />
</head>

<body>
  <div class="container">
    <h1>Patient</h1>
    <form action="#" method="get">
      <div class="register_box">
        <!-- Full name  -->
        <div class="input">
          <label for="fullName">Full Name</label>
          <input type="text" id="fullName" name="fullName" placeholder="Enter Full Name" required />
        </div>
        <!-- Email  -->
        <div class="input">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Enter Email" required />
        </div>
        <!-- Phone number  -->
        <div class="input">
          <label for="phoneNumber">Phone Number</label>
          <input type="text" id="phoneNumber" name="phoneNumber" placeholder="Enter Phone Number" required />
        </div>
        <!-- Age  -->
        <div class="input">
          <label for="phoneNumber">Age</label>
          <input type="text" id="age" name="age" placeholder="Enter Age" required />
        </div>
        <!-- Birth Year  -->
        <div class="input">
          <label for="birthYear">Birth Year</label>
          <input type="text" id="birthYear" name="birthYear" placeholder="Enter Birth Year Eg:- 1997" required />
        </div>
        <div class="input">
          <label for="address">Address</label>
          <input type="text" id="address" name="address" placeholder="Enter Address" required />
        </div>
        <!-- Password  -->
        <div class="input">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter Password" required />
        </div>
        <div class="input">
          <label for="confirmPassword">Confirm Password</label>
          <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required />
        </div>
      </div>
      <!-- Gender  -->
      <span class="gender">Gender</span>
      <div class="genders">
        <input type="radio" name="gender" id="male" required>
        <label for="male">Male</label>
        <input type="radio" name="gender" id="female">
        <label for="female">Female</label>
        <input type="radio" name="gender" id="other">
        <label for="other">Other</label>
      </div>

      <div class="submit">
        <input type="submit" value="Register">
      </div>
    </form>
    <div class="already">
        <label for="login">Already have an account: <a href="#">Login</a></label>
      </div>
  </div>
</body>

</html>