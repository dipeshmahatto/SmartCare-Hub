<?php
require_once '../security.php';
secure_session_start();
if (!isset($_SESSION['Adminloggedin']) || $_SESSION['Adminloggedin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

include 'query/counts.php';

$activePage = 'dashboard';
$pageTitle = 'Admin Command Center';
$pageDescription = 'Live system operations';

$specialities = [];
$specialityResult = mysqli_query($conn, "SELECT speciality, COUNT(*) AS total FROM doctor GROUP BY speciality ORDER BY total DESC, speciality ASC");
if ($specialityResult) {
    while ($row = $specialityResult->fetch_assoc()) {
        $specialities[] = $row;
    }
}
$maxSpecialityCount = 0;
foreach ($specialities as $speciality) {
    $maxSpecialityCount = max($maxSpecialityCount, (int) $speciality['total']);
}

$recentAppointments = [];
$recentResult = mysqli_query($conn, "SELECT a.aid, a.category, a.doctor, a.app_time, a.day, a.status, p.fullName AS patient_name FROM appointment a LEFT JOIN patient p ON p.id = a.pid ORDER BY a.aid DESC LIMIT 6");
if ($recentResult) {
    while ($row = $recentResult->fetch_assoc()) {
        $recentAppointments[] = $row;
    }
}

$pendingApprovals = [];
$approvalResult = mysqli_query($conn, "SELECT id, fullName, speciality, qualification, address FROM doctor_approval ORDER BY id DESC LIMIT 4");
if ($approvalResult) {
    while ($row = $approvalResult->fetch_assoc()) {
        $pendingApprovals[] = $row;
    }
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function initials($name, $fallback = 'SC')
{
    $result = '';
    foreach (preg_split('/\s+/', trim((string) $name)) as $part) {
        if ($part !== '') {
            $result .= strtoupper(substr($part, 0, 1));
        }
    }
    return substr($result ?: $fallback, 0, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SmartCare Hub admin command center for managing doctors, patients, approvals and appointment operations.">
    <link rel="stylesheet" href="../../css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Admin Command Center | SmartCare Hub</title>
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
        <?php include 'top.php'; ?>

        <section class="admin-hero">
            <div>
                <span class="admin-eyebrow"><i class="fa-solid fa-chart-line"></i> SmartCare operations</span>
                <h1>Everything that matters, in one command center.</h1>
                <p>Monitor provider capacity, patient growth, approval requests and appointment activity using live records already stored in SmartCare.</p>
                <div class="admin-hero-actions">
                    <a href="approval.php" class="admin-primary-action"><i class="fa-solid fa-user-check"></i> Review approvals<?php if ($approvals > 0): ?><span><?= $approvals ?></span><?php endif; ?></a>
                    <a href="doctor_list.php" class="admin-secondary-action"><i class="fa-solid fa-users-gear"></i> Manage providers</a>
                </div>
            </div>
            <div class="health-score-card">
                <span>Appointment completion</span>
                <div class="health-score-ring" style="--completion: <?= max(0, min(100, $completionRate)) ?>%;">
                    <strong><?= $completionRate ?>%</strong>
                </div>
                <small><?= $completedAppointments ?> completed of <?= $totalAppointments ?> recorded</small>
            </div>
        </section>

        <section class="admin-kpi-grid" aria-label="SmartCare system overview">
            <article class="admin-kpi-card kpi-blue">
                <div class="kpi-icon"><i class="fa-solid fa-user-doctor"></i></div>
                <div><span>Verified doctors</span><strong><?= $doctors ?></strong><small><?= $totalSpecialities ?> active specialties</small></div>
                <a href="doctor_list.php" aria-label="Open doctor directory"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </article>
            <article class="admin-kpi-card kpi-violet">
                <div class="kpi-icon"><i class="fa-solid fa-hospital-user"></i></div>
                <div><span>Registered patients</span><strong><?= $patients ?></strong><small>Patient accounts in SmartCare</small></div>
                <a href="patient_list.php" aria-label="Open patient directory"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </article>
            <article class="admin-kpi-card kpi-amber">
                <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                <div><span>Pending approvals</span><strong><?= $approvals ?></strong><small><?= $approvals === 1 ? 'Doctor application' : 'Doctor applications' ?> waiting</small></div>
                <a href="approval.php" aria-label="Open pending approvals"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            </article>
            <article class="admin-kpi-card kpi-green">
                <div class="kpi-icon"><i class="fa-regular fa-calendar-check"></i></div>
                <div><span>Appointments</span><strong><?= $totalAppointments ?></strong><small><?= $pendingAppointments ?> pending · <?= $confirmedAppointments ?> confirmed</small></div>
                <span class="kpi-trend"><i class="fa-solid fa-chart-simple"></i> Live total</span>
            </article>
        </section>

        <section class="admin-dashboard-grid">
            <article class="admin-panel appointment-analytics-panel">
                <div class="admin-panel-heading">
                    <div>
                        <span>Appointment analytics</span>
                        <h2>Current workload</h2>
                        <p>A real-time breakdown of every appointment lifecycle state.</p>
                    </div>
                    <span class="panel-badge"><i class="fa-solid fa-database"></i> Live data</span>
                </div>

                <div class="appointment-breakdown">
                    <div class="appointment-donut" style="--completed: <?= max(0, min(100, $completionRate)) ?>%;">
                        <div><strong><?= $totalAppointments ?></strong><span>Total</span></div>
                    </div>
                    <div class="breakdown-legend">
                        <div><span class="legend-dot pending-dot"></span><div><small>Pending requests</small><strong><?= $pendingAppointments ?></strong></div></div>
                        <div><span class="legend-dot confirmed-dot"></span><div><small>Confirmed appointments</small><strong><?= $confirmedAppointments ?></strong></div></div>
                        <div><span class="legend-dot completed-dot"></span><div><small>Completed visits</small><strong><?= $completedAppointments ?></strong></div></div>
                        <div><span class="legend-dot cancelled-dot"></span><div><small>Cancelled records</small><strong><?= $cancelledAppointments ?></strong></div></div>
                    </div>
                </div>

                <div class="analytics-note">
                    <i class="fa-solid fa-circle-info"></i>
                    <p>SmartCare currently records weekday and time slots rather than calendar dates, so this dashboard intentionally avoids fabricated daily or monthly trends.</p>
                </div>
            </article>

            <article class="admin-panel speciality-panel">
                <div class="admin-panel-heading compact-heading">
                    <div>
                        <span>Provider mix</span>
                        <h2>Specialty distribution</h2>
                    </div>
                    <a href="doctor_list.php">Directory <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <?php if (!empty($specialities)): ?>
                    <div class="speciality-bars">
                        <?php foreach ($specialities as $speciality):
                            $count = (int) $speciality['total'];
                            $width = $maxSpecialityCount > 0 ? max(12, (int) round(($count / $maxSpecialityCount) * 100)) : 0;
                        ?>
                            <div class="speciality-row">
                                <div><strong><?= e($speciality['speciality']) ?></strong><span><?= $count ?> <?= $count === 1 ? 'doctor' : 'doctors' ?></span></div>
                                <div class="speciality-track"><span style="width: <?= $width ?>%"></span></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="admin-empty compact-empty"><i class="fa-solid fa-stethoscope"></i><strong>No specialties yet</strong><p>Approved doctors will appear here automatically.</p></div>
                <?php endif; ?>
            </article>
        </section>

        <section class="admin-dashboard-grid lower-grid">
            <article class="admin-panel records-panel">
                <div class="admin-panel-heading compact-heading">
                    <div>
                        <span>Latest records</span>
                        <h2>Appointment activity</h2>
                        <p>Newest appointment records, ordered by booking ID.</p>
                    </div>
                    <span class="panel-badge muted-badge">#ID order</span>
                </div>

                <?php if (!empty($recentAppointments)): ?>
                    <div class="activity-list">
                        <?php foreach ($recentAppointments as $appointment): ?>
                            <div class="activity-item">
                                <div class="activity-avatar"><?= e(initials($appointment['patient_name'] ?? 'Patient', 'PT')) ?></div>
                                <div class="activity-copy">
                                    <div><strong><?= e($appointment['patient_name'] ?: 'Patient #' . $appointment['aid']) ?></strong><span>#<?= (int) $appointment['aid'] ?></span></div>
                                    <p><?= e($appointment['category']) ?> with <?= e($appointment['doctor']) ?></p>
                                    <small><i class="fa-regular fa-calendar"></i> <?= e(ucfirst(strtolower($appointment['day']))) ?> · <?= e($appointment['app_time']) ?></small>
                                </div>
                                <?php $recordStatus = appointmentStatusMeta($appointment['status']); ?>
                                <span class="record-status <?= e($recordStatus['class']) ?>">
                                    <i class="<?= e($recordStatus['icon']) ?>"></i><?= e($recordStatus['label']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="admin-empty"><i class="fa-regular fa-calendar-xmark"></i><strong>No appointment records</strong><p>Bookings will appear here when patients create appointments.</p></div>
                <?php endif; ?>
            </article>

            <article class="admin-panel approvals-preview-panel">
                <div class="admin-panel-heading compact-heading">
                    <div>
                        <span>Provider onboarding</span>
                        <h2>Approval queue</h2>
                    </div>
                    <a href="approval.php">Review all <i class="fa-solid fa-arrow-right"></i></a>
                </div>

                <?php if (!empty($pendingApprovals)): ?>
                    <div class="approval-preview-list">
                        <?php foreach ($pendingApprovals as $request): ?>
                            <a href="approval.php" class="approval-preview-item">
                                <div class="approval-avatar"><?= e(initials($request['fullName'], 'DR')) ?></div>
                                <div><strong><?= e($request['fullName']) ?></strong><span><?= e($request['speciality']) ?> · <?= e($request['qualification']) ?></span><small><?= e($request['address']) ?></small></div>
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="admin-empty compact-empty"><i class="fa-solid fa-user-check"></i><strong>Queue cleared</strong><p>There are no doctor registrations awaiting review.</p></div>
                <?php endif; ?>
            </article>
        </section>

        <section class="admin-quick-actions">
            <div>
                <span>Management shortcuts</span>
                <h2>Jump straight into operations</h2>
            </div>
            <div class="quick-action-grid">
                <a href="doctor_list.php"><i class="fa-solid fa-user-doctor"></i><div><strong>Doctor directory</strong><span>Review verified providers</span></div><i class="fa-solid fa-arrow-right"></i></a>
                <a href="patient_list.php"><i class="fa-solid fa-hospital-user"></i><div><strong>Patient directory</strong><span>Browse registered patients</span></div><i class="fa-solid fa-arrow-right"></i></a>
                <a href="approval.php"><i class="fa-solid fa-user-check"></i><div><strong>Approval center</strong><span><?= $approvals ?> pending <?= $approvals === 1 ? 'request' : 'requests' ?></span></div><i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </section>

        <footer class="admin-workspace-footer">
            <span><i class="fa-solid fa-shield-heart"></i> SmartCare Hub Operations</span>
            <span>Live counts come directly from the current database.</span>
        </footer>
    </main>
</div>
<script src="../../js/admin-workspace.js"></script>
  <script src="../../js/system.js" defer></script>
</body>
</html>
