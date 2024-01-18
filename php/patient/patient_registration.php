<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_registration.css">
    <title>Registration</title>
</head>

<body>
    <div class="container">
        <h1>
            Register as patient
        </h1>
        <hr>

        <form action="" method="post">
            <label for="name">Name :</label>
            <input type="text" name="name" id="name" placeholder="Enter your full name">

            <label for="age">Age :</label>
            <input type="number" name="age" id="age" placeholder="Enter you age">
            <br>
            <label for="gender">Gender :</label>
            <input type="radio" name="gender" id="gender">Male</input>
            <input type="radio" name="gender" id="gender">Female</input>
            <br>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" placeholder="Enter your email">

            <label for="number">Number :</label>
            <input type="number" name="number" id="number" placeholder="Enter your mobile number">
            <br>
            <label for="address">Address :</label>
            <input type="text" name="address" id="address" placeholder="Enter your address">

            <label for="birth_year">Birth Year :</label>
            <input type="number" name="birth_year" id="birth_year" placeholder="eg:-1975">
            <br>
            <label for="password">Password :</label>
            <input type="password" name="password" id="password" placeholder="Create new password">
        </form>

    </div>
</body>

</html>