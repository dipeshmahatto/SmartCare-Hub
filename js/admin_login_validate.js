function validateForm() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if (username === "" || password === "") {
        alert("Please fill the both username and password.");
        return false; // Prevent the form from being submitted
    }

    // If validation passes, you can proceed with submitting the form
    return true;
}