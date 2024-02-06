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
        <form action="query/verify_user.php" method="post">
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
</body>

</html>