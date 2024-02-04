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
        <form action="" method="get" onsubmit="displaypassword()" id="verify">
            <div class="inputbox">
                <label for="phoneNumber">Mobile Number :</label>
                <input type="text" id="phoneNumber" name="phoneNumber" maxlength="10" required>
            </div>
            <div class="inputbox">
                <label for="birthYear">Birth Year:</label>
                <input type="text" id="birthYear" name="birthYear" required>
            </div>
            <div class="inputbox">
                <label for="newPassword">New Password:</label>
                <input type="text" id="newPassword" name="newPassword" required>
            </div>
            <div class="inputbox">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="text" id="confirmPassword" name="confirmPassword" required>
            </div>
            <div class="submit">
                <input type="submit" value="Verify User">
            </div>
        </form>
    </div>
</body>
</html>