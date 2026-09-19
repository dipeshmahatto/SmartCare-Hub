<?php
require_once '../security.php';
secure_session_start();
include 'query/counts.php';
if (!isset($_SESSION['Adminloggedin']) || $_SESSION['Adminloggedin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

$activePage = 'patients';
$pageTitle = 'Patient Directory';
$pageDescription = 'Registered patient overview';

$patientList = [];
$result = mysqli_query($conn, "SELECT p.*, COUNT(a.aid) AS appointment_count,
    SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
    SUM(CASE WHEN a.status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
    SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_count
    FROM patient p LEFT JOIN appointment a ON a.pid = p.id GROUP BY p.id ORDER BY p.fullName ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patientList[] = $row;
    }
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function initials($name)
{
    $initials = '';
    foreach (preg_split('/\s+/', trim((string) $name)) as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    return substr($initials ?: 'PT', 0, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Patient Directory | SmartCare Hub</title>
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>
    <main class="admin-main">
        <?php include 'top.php'; ?>

        <section class="directory-hero patient-directory-hero">
            <div>
                <span class="admin-eyebrow"><i class="fa-solid fa-hospital-user"></i> Patient overview</span>
                <h1>Registered patient directory</h1>
                <p>See patient contact information and appointment activity without changing clinical records.</p>
            </div>
            <div class="directory-summary">
                <div><strong><?= $patients ?></strong><span>Patients</span></div>
                <div><strong><?= $totalAppointments ?></strong><span>Appointments</span></div>
                <div class="summary-pill"><i class="fa-solid fa-circle-check"></i><?= $completedAppointments ?> completed</div>
            </div>
        </section>

        <section class="directory-toolbar">
            <label class="admin-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Search patient, email, phone or location" data-admin-search="patient-directory">
                <span class="search-shortcut">Search</span>
            </label>
            <div class="directory-count"><i class="fa-solid fa-database"></i><span id="patient-result-count"><?= count($patientList) ?></span> visible records</div>
        </section>

        <?php if (!empty($patientList)): ?>
            <section class="management-card-grid patient-card-grid" id="patient-directory">
                <?php foreach ($patientList as $patient):
                    $search = strtolower(implode(' ', [$patient['fullName'], $patient['email'], $patient['phoneNumber'], $patient['address']]));
                    $appointmentCount = (int) ($patient['appointment_count'] ?? 0);
                    $pendingCount = (int) ($patient['pending_count'] ?? 0);
                    $confirmedCount = (int) ($patient['confirmed_count'] ?? 0);
                    $completedCount = (int) ($patient['completed_count'] ?? 0);
                    $cancelledCount = (int) ($patient['cancelled_count'] ?? 0);
                ?>
                    <article class="management-card patient-management-card" data-search-text="<?= e($search) ?>">
                        <div class="management-card-head">
                            <div class="directory-avatar patient-directory-avatar"><?= e(initials($patient['fullName'])) ?></div>
                            <div class="management-identity">
                                <span>Patient #<?= (int) $patient['id'] ?></span>
                                <h2><?= e($patient['fullName']) ?></h2>
                                <p><?= e($patient['address']) ?> · <?= (int) $patient['age'] ?> years</p>
                            </div>
                            <span class="patient-badge"><i class="fa-solid fa-user-check"></i> Registered</span>
                        </div>

                        <div class="patient-appointment-stats lifecycle-stats">
                            <div><span>Total</span><strong><?= $appointmentCount ?></strong></div>
                            <div><span>Pending</span><strong><?= $pendingCount ?></strong></div>
                            <div><span>Confirmed</span><strong><?= $confirmedCount ?></strong></div>
                            <div><span>Completed</span><strong><?= $completedCount ?></strong></div>
                            <div><span>Cancelled</span><strong><?= $cancelledCount ?></strong></div>
                        </div>

                        <div class="management-detail-grid">
                            <div><i class="fa-regular fa-envelope"></i><span><small>Email</small><strong><?= e($patient['email']) ?></strong></span></div>
                            <div><i class="fa-solid fa-phone"></i><span><small>Phone</small><strong><?= e($patient['phoneNumber']) ?></strong></span></div>
                            <div><i class="fa-solid fa-location-dot"></i><span><small>Address</small><strong><?= e($patient['address']) ?></strong></span></div>
                            <div><i class="fa-regular fa-id-card"></i><span><small>Profile</small><strong><?= e(strtoupper($patient['gender'])) ?> · Born <?= (int) $patient['birthYear'] ?></strong></span></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
            <div class="filter-empty-state" id="patient-directory-empty" hidden><i class="fa-solid fa-magnifying-glass"></i><strong>No patients match that search</strong><p>Try a name, email, phone number or location.</p></div>
        <?php else: ?>
            <div class="admin-empty large-empty"><i class="fa-solid fa-hospital-user"></i><strong>No registered patients</strong><p>Patient accounts will appear here after registration.</p></div>
        <?php endif; ?>

        <footer class="admin-workspace-footer"><span><i class="fa-solid fa-shield-heart"></i> SmartCare Hub Operations</span><span>Patient records: <?= count($patientList) ?></span></footer>
    </main>
</div>
<script src="../../js/admin-workspace.js"></script>
  <script src="../../js/system.js" defer></script>
</body>
</html>
