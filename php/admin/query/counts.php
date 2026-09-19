<?php
include '../database.php';
require_once __DIR__ . '/../../appointment_status.php';

function scalarCount($conn, $sql, $field)
{
    $result = mysqli_query($conn, $sql);
    if (!$result) return 0;
    $row = $result->fetch_assoc();
    return (int) ($row[$field] ?? 0);
}

$doctors = scalarCount($conn, 'SELECT COUNT(*) AS doctors FROM doctor', 'doctors');
$patients = scalarCount($conn, 'SELECT COUNT(*) AS patients FROM patient', 'patients');
$approvals = scalarCount($conn, 'SELECT COUNT(*) AS approvals FROM doctor_approval', 'approvals');
$totalAppointments = scalarCount($conn, 'SELECT COUNT(*) AS total FROM appointment', 'total');
$pendingAppointments = scalarCount($conn, "SELECT COUNT(*) AS total FROM appointment WHERE status = 'pending'", 'total');
$confirmedAppointments = scalarCount($conn, "SELECT COUNT(*) AS total FROM appointment WHERE status = 'confirmed'", 'total');
$completedAppointments = scalarCount($conn, "SELECT COUNT(*) AS total FROM appointment WHERE status = 'completed'", 'total');
$cancelledAppointments = scalarCount($conn, "SELECT COUNT(*) AS total FROM appointment WHERE status = 'cancelled'", 'total');
$activeAppointments = $pendingAppointments + $confirmedAppointments;
$totalSpecialities = scalarCount($conn, 'SELECT COUNT(DISTINCT speciality) AS total FROM doctor', 'total');
$careLifecycleTotal = $pendingAppointments + $confirmedAppointments + $completedAppointments;
$completionRate = $careLifecycleTotal > 0 ? (int) round(($completedAppointments / $careLifecycleTotal) * 100) : 0;
