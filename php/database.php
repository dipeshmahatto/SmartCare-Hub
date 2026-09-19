<?php
$host = getenv('SMARTCARE_DB_HOST') ?: 'localhost';
$unamee = getenv('SMARTCARE_DB_USER') ?: 'root';
$passwordd = getenv('SMARTCARE_DB_PASSWORD') ?: '';
$db_name = getenv('SMARTCARE_DB_NAME') ?: 'has';
$port = (int) (getenv('SMARTCARE_DB_PORT') ?: 3306);

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect($host, $unamee, $passwordd, $db_name, $port);

if (!$conn) {
    http_response_code(500);
    error_log('SmartCare database connection failed: ' . mysqli_connect_error());
    exit('SmartCare is temporarily unable to connect to its database.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
