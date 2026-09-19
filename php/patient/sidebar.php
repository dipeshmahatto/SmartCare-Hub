<?php
$patientActivePage = $patientActivePage ?? '';
$patientInitials = '';
if (!empty($fullName)) {
    $nameParts = preg_split('/\s+/', trim($fullName));
    foreach (array_slice($nameParts, 0, 2) as $part) {
        if ($part !== '') {
            $patientInitials .= strtoupper(substr($part, 0, 1));
        }
    }
}
if ($patientInitials === '') {
    $patientInitials = 'P';
}
?>
<aside class="nav patient-sidebar">
    <a class="portal-brand" href="patient_dashboard.php" aria-label="SmartCare patient dashboard">
        <span class="portal-brand__mark"><i class="fa-solid fa-heart-pulse"></i></span>
        <span>
            <strong>SmartCare</strong>
            <small>Patient Portal</small>
        </span>
    </a>

    <div class="profile patient-profile">
        <div class="patient-avatar-wrap">
            <?php if (!empty($imagePath)): ?>
                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($fullName) ?> profile photo">
            <?php else: ?>
                <div class="patient-avatar-fallback" aria-label="Profile initials"><?= htmlspecialchars($patientInitials) ?></div>
            <?php endif; ?>
            <span class="avatar-status" title="Signed in"></span>
        </div>
        <h3><?= htmlspecialchars($fullName) ?></h3>
        <p>Patient account</p>

        <?php if (empty($imagePath)): ?>
            <div class="upload-container" id="upload-container">
                <form action="upload.php" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
                    <label class="upload-trigger" for="file"><i class="fa-solid fa-camera"></i> Add photo</label>
                    <input class="visually-hidden-file" type="file" name="file" id="file" accept="image/*" onchange="this.form.submit()">
                </form>
            </div>
        <?php endif; ?>
    </div>

    <nav class="operation patient-nav" aria-label="Patient navigation">
        <a href="patient_dashboard.php" class="<?= $patientActivePage === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span>
        </a>
        <a href="appointment.php" class="<?= $patientActivePage === 'appointment' ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-plus"></i><span>Book Appointment</span>
        </a>
        <a href="history.php" class="<?= $patientActivePage === 'history' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left"></i><span>Appointment History</span>
        </a>
    </nav>

    <div class="sidebar-help">
        <span><i class="fa-solid fa-shield-heart"></i></span>
        <div>
            <strong>Your care, organized.</strong>
            <p>Manage appointments in one secure place.</p>
        </div>
    </div>
</aside>
