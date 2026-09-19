<?php
$pageTitle = $pageTitle ?? 'Admin Workspace';
$pageDescription = $pageDescription ?? 'SmartCare operations';
?>
<header class="admin-topbar">
    <div class="admin-topbar-brand">
        <img src="../../img/logo.png" alt="SmartCare Hub logo">
        <div>
            <span>SmartCare Hub</span>
            <strong><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
    </div>
    <div class="admin-topbar-meta">
        <button class="theme-toggle" type="button" data-theme-toggle><span data-theme-icon aria-hidden="true"></span><span class="theme-toggle__label">Theme</span></button>
        <span><i class="fa-solid fa-shield-halved"></i><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></span>
        <a href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Sign out</span></a>
    </div>
</header>
