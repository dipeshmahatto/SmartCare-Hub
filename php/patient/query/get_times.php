<?php
include '../../database.php';

$selectedDoctor = trim($_POST['doctor'] ?? '');
$selectedDay = strtoupper(trim($_POST['day'] ?? ''));

if ($selectedDoctor === '' || $selectedDay === '') {
    echo "<option value=''>Select a doctor and day first</option>";
    exit;
}

$bookedTimes = [];
$stmt = $conn->prepare("SELECT app_time FROM appointment WHERE day = ? AND doctor = ? AND status IN ('pending','confirmed')");
if ($stmt) {
    $stmt->bind_param('ss', $selectedDay, $selectedDoctor);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $bookedTimes[] = $row['app_time'];
    }
    $stmt->close();
}

$result = $conn->query('SELECT times FROM times ORDER BY tid ASC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['times'], $bookedTimes, true)) {
            $safe = htmlspecialchars($row['times'], ENT_QUOTES, 'UTF-8');
            echo "<option value='{$safe}'>{$safe}</option>";
        }
    }
}
