<?php
$pageTitle = $pageTitle ?? 'Patient Portal';
$pageEyebrow = $pageEyebrow ?? 'SmartCare';
?>
<header class="top patient-topbar">
    <div class="topbar-copy">
        <span><?= htmlspecialchars($pageEyebrow) ?></span>
        <strong><?= htmlspecialchars($pageTitle) ?></strong>
    </div>
    <div class="topbar-actions">
        <button class="theme-toggle" type="button" data-theme-toggle><span data-theme-icon aria-hidden="true"></span><span class="theme-toggle__label">Theme</span></button>
        <a class="topbar-home" href="../index.php"><i class="fa-solid fa-house"></i><span>Home</span></a>
        <a class="topbar-logout" href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Logout</span></a>
    </div>
</header>
