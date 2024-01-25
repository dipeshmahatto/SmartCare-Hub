function validateForm() {
    var phoneNumber = document.getElementById("phoneNumber").value;
    var password = document.getElementById("password").value;

    if (phoneNumber === "" || password === "") {
        alert("Please fill in both username and password.");
        return false; // Prevent the form from being submitted
    }

    // If validation passes, you can proceed with submitting the form
    return true;
}