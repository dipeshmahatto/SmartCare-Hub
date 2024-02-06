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
        <form action="query/password.php" method="post">
            <div class="inputbox">
                <label for="newPassword">New Password:</label>
                <input type="text" id="newPassword" name="newPassword" required>
            </div>
            <div class="inputbox">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="text" id="confirmPassword" name="confirmPassword" required>
            </div>
            <div class="submit">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
</body>
</html>