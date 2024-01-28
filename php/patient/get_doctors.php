<?php
include "../database.php";

if (isset($_POST['category'])) {
    $selectedCategory = $_POST['category'];
    $sql = "SELECT fullName FROM doctor WHERE speciality = '$selectedCategory'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['fullName'] . "'>" . $row['fullName'] . "</option>";
        }
    } else {
        echo "<option value=''>No doctors found</option>";
    }
    $result->close();
} else {
    echo "<option value=''>Select a category first</option>";
}
$stmt->close();
$conn->close();
?>