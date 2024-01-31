<?php
include "../database.php";

if (isset($_POST['doctor']) && isset($_POST['day'])) {
    $selectedDay = $_POST['day'];
    $selectedDoctor = $_POST['doctor'];
    $sql = "SELECT app_time FROM appointment WHERE day = '$selectedDay' AND doctor = '$selectedDoctor' AND status=0";
    $timesql = "SELECT times FROM times";
    $result = $conn->query($sql);
    $result2 = $conn->query($timesql);

    // Fetch all booked appointment times
    $bookedTimes = [];
    while ($row = $result->fetch_assoc()) {
        $bookedTimes[] = $row['app_time'];
    }

    // Output options for available times
    if ($result2->num_rows > 0) {
        while ($row = $result2->fetch_assoc()) {
            // Check if the time is not already booked
            if (!in_array($row['times'], $bookedTimes)) {
                echo "<option value='" . $row['times'] . "'>" . $row['times'] . "</option>";
            }
        }
    }

} else {
    echo "<option value=''>Select a day first</option>";
}
?>
