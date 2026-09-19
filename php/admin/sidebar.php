<?php
$activePage = $activePage ?? '';
?>
<aside class="admin-sidebar">
    <a class="admin-brand" href="admin_dashboard.php" aria-label="SmartCare Hub Admin Command Center">
        <img src="../../img/logo.jpg" alt="SmartCare Hub">
    </a>

    <div class="admin-identity">
        <img src="../../img/admin_profile.jpg" alt="Administrator profile">
        <div>
            <span>Signed in as</span>
            <strong>Administrator</strong>
            <small><i class="fa-solid fa-circle-check"></i> System access</small>
        </div>
    </div>

    <nav class="admin-nav" aria-label="Admin navigation">
        <a href="admin_dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-table-cells-large"></i><span>Command center</span>
        </a>
        <a href="doctor_list.php" class="<?= $activePage === 'doctors' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-doctor"></i><span>Doctors</span><b><?= (int) $doctors ?></b>
        </a>
        <a href="patient_list.php" class="<?= $activePage === 'patients' ? 'active' : '' ?>">
            <i class="fa-solid fa-hospital-user"></i><span>Patients</span><b><?= (int) $patients ?></b>
        </a>
        <a href="approval.php" class="<?= $activePage === 'approvals' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-check"></i><span>Approvals</span><b class="<?= $approvals > 0 ? 'attention' : '' ?>"><?= (int) $approvals ?></b>
        </a>
    </nav>

    <div class="admin-sidebar-card">
        <i class="fa-solid fa-shield-heart"></i>
        <div>
            <strong>Operations overview</strong>
            <p>Manage people, approvals and appointment activity from one workspace.</p>
        </div>
    </div>

    <a class="admin-signout" href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign out</a>
</aside>
