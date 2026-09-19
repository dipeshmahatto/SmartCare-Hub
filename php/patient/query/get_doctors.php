<?php
include '../../database.php';

$selectedCategory = trim((string) ($_POST['selectedCategory'] ?? ''));
if ($selectedCategory === '') {
    echo "<option value=''>Select a category first</option>";
    exit;
}

$stmt = $conn->prepare('SELECT fullName FROM doctor WHERE speciality = ? ORDER BY fullName ASC');
$stmt->bind_param('s', $selectedCategory);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<option value=''>No doctors available</option>";
} else {
    echo "<option value=''>Select Doctor</option>";
    while ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['fullName'], ENT_QUOTES, 'UTF-8');
        echo "<option value='{$name}'>{$name}</option>";
    }
}
$stmt->close();
