<?php
require_once '../security.php';
secure_session_start();
include 'query/counts.php';
if (!isset($_SESSION['Adminloggedin']) || $_SESSION['Adminloggedin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

$activePage = 'doctors';
$pageTitle = 'Doctor Directory';
$pageDescription = 'Verified provider management';

$doctorsList = [];
$result = mysqli_query($conn, 'SELECT * FROM doctor ORDER BY fullName ASC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctorsList[] = $row;
    }
}

$specialities = [];
foreach ($doctorsList as $doctor) {
    $specialities[$doctor['speciality']] = ($specialities[$doctor['speciality']] ?? 0) + 1;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function initials($name)
{
    $initials = '';
    foreach (preg_split('/\s+/', trim((string) preg_replace('/^Dr\.?/i', '', $name))) as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    return substr($initials ?: 'DR', 0, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Doctor Directory | SmartCare Hub</title>
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>
    <main class="admin-main">
        <?php include 'top.php'; ?>

        <section class="directory-hero">
            <div>
                <span class="admin-eyebrow"><i class="fa-solid fa-user-doctor"></i> Provider management</span>
                <h1>Verified doctor directory</h1>
                <p>Browse the active provider network and remove a doctor only when their SmartCare access should be revoked.</p>
            </div>
            <div class="directory-summary">
                <div><strong><?= $doctors ?></strong><span>Doctors</span></div>
                <div><strong><?= count($specialities) ?></strong><span>Specialties</span></div>
                <a href="approval.php"><i class="fa-solid fa-user-check"></i> <?= $approvals ?> pending</a>
            </div>
        </section>

        <section class="directory-toolbar">
            <label class="admin-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="search" placeholder="Search doctor, specialty, qualification or location" data-admin-search="doctor-directory">
                <span class="search-shortcut">Search</span>
            </label>
            <div class="directory-count"><i class="fa-solid fa-database"></i><span id="doctor-result-count"><?= count($doctorsList) ?></span> visible records</div>
        </section>

        <?php if (!empty($doctorsList)): ?>
            <section class="management-card-grid" id="doctor-directory">
                <?php foreach ($doctorsList as $doctor):
                    $search = strtolower(implode(' ', [$doctor['fullName'], $doctor['speciality'], $doctor['qualification'], $doctor['address'], $doctor['email'], $doctor['phoneNumber']]));
                ?>
                    <article class="management-card" data-search-text="<?= e($search) ?>">
                        <div class="management-card-head">
                            <div class="directory-avatar doctor-directory-avatar"><?= e(initials($doctor['fullName'])) ?></div>
                            <div class="management-identity">
                                <span>Doctor #<?= (int) $doctor['id'] ?></span>
                                <h2><?= e($doctor['fullName']) ?></h2>
                                <p><?= e($doctor['speciality']) ?> · <?= e($doctor['qualification']) ?></p>
                            </div>
                            <span class="verified-badge"><i class="fa-solid fa-circle-check"></i> Verified</span>
                        </div>

                        <div class="management-detail-grid">
                            <div><i class="fa-regular fa-envelope"></i><span><small>Email</small><strong><?= e($doctor['email']) ?></strong></span></div>
                            <div><i class="fa-solid fa-phone"></i><span><small>Phone</small><strong><?= e($doctor['phoneNumber']) ?></strong></span></div>
                            <div><i class="fa-solid fa-location-dot"></i><span><small>Location</small><strong><?= e($doctor['address']) ?></strong></span></div>
                            <div><i class="fa-regular fa-id-card"></i><span><small>Profile</small><strong><?= (int) $doctor['age'] ?> yrs · <?= e(strtoupper($doctor['gender'])) ?></strong></span></div>
                        </div>

                        <div class="management-card-footer">
                            <span><i class="fa-solid fa-stethoscope"></i><?= e($doctor['speciality']) ?></span>
                            <form action="query/delete.php" method="post" class="js-confirm-form">
            <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int) $doctor['id'] ?>">
                                <input type="hidden" name="fullName" value="<?= e($doctor['fullName']) ?>">
                                <button type="submit" name="delete" class="danger-action"
                                    data-confirm-title="Remove <?= e($doctor['fullName']) ?>?"
                                    data-confirm-message="This removes the doctor and their appointments from SmartCare. This action cannot be undone from the dashboard."
                                    data-confirm-label="Remove doctor"
                                    data-confirm-tone="danger">
                                    <i class="fa-solid fa-user-minus"></i> Remove
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
            <div class="filter-empty-state" id="doctor-directory-empty" hidden><i class="fa-solid fa-magnifying-glass"></i><strong>No doctors match that search</strong><p>Try a name, specialty, qualification or location.</p></div>
        <?php else: ?>
            <div class="admin-empty large-empty"><i class="fa-solid fa-user-doctor"></i><strong>No verified doctors</strong><p>Approved doctor accounts will appear in this directory.</p><a href="approval.php">Open approval center</a></div>
        <?php endif; ?>

        <footer class="admin-workspace-footer"><span><i class="fa-solid fa-shield-heart"></i> SmartCare Hub Operations</span><span>Doctor records: <?= count($doctorsList) ?></span></footer>
    </main>
</div>
<?php include 'confirm_modal.php'; ?>
<script src="../../js/admin-workspace.js"></script>
  <script src="../../js/system.js" defer></script>
</body>
</html>
