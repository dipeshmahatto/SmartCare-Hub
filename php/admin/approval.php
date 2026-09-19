<?php
require_once '../security.php';
secure_session_start();
include 'query/counts.php';
if (!isset($_SESSION['Adminloggedin']) || $_SESSION['Adminloggedin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

$activePage = 'approvals';
$pageTitle = 'Approval Center';
$pageDescription = 'Doctor registration review';

$approvalList = [];
$result = mysqli_query($conn, 'SELECT * FROM doctor_approval ORDER BY id DESC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $approvalList[] = $row;
    }
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
    <title>Approval Center | SmartCare Hub</title>
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>
    <main class="admin-main">
        <?php include 'top.php'; ?>

        <section class="approval-hero">
            <div>
                <span class="admin-eyebrow"><i class="fa-solid fa-user-check"></i> Provider onboarding</span>
                <h1>Doctor approval center</h1>
                <p>Review pending doctor registrations before they enter the verified SmartCare provider directory.</p>
            </div>
            <div class="approval-hero-count">
                <span>Waiting for review</span>
                <strong><?= $approvals ?></strong>
                <small><?= $approvals === 1 ? 'application' : 'applications' ?></small>
            </div>
        </section>

        <?php if (!empty($approvalList)): ?>
            <section class="directory-toolbar approval-toolbar">
                <label class="admin-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" placeholder="Search applicant, specialty, qualification or location" data-admin-search="approval-directory">
                    <span class="search-shortcut">Search</span>
                </label>
                <div class="directory-count"><i class="fa-solid fa-hourglass-half"></i><span id="approval-result-count"><?= count($approvalList) ?></span> visible requests</div>
            </section>

            <section class="approval-grid" id="approval-directory">
                <?php foreach ($approvalList as $request):
                    $search = strtolower(implode(' ', [$request['fullName'], $request['email'], $request['speciality'], $request['qualification'], $request['address'], $request['phoneNumber']]));
                ?>
                    <article class="approval-card" data-search-text="<?= e($search) ?>">
                        <div class="approval-card-top">
                            <div class="directory-avatar approval-directory-avatar"><?= e(initials($request['fullName'])) ?></div>
                            <div class="approval-card-title">
                                <span>Application #<?= (int) $request['id'] ?></span>
                                <h2><?= e($request['fullName']) ?></h2>
                                <p><?= e($request['speciality']) ?> · <?= e($request['qualification']) ?></p>
                            </div>
                            <span class="pending-badge"><i class="fa-solid fa-clock"></i> Pending</span>
                        </div>

                        <div class="approval-profile-grid">
                            <div><i class="fa-regular fa-envelope"></i><span><small>Email</small><strong><?= e($request['email']) ?></strong></span></div>
                            <div><i class="fa-solid fa-phone"></i><span><small>Phone</small><strong><?= e($request['phoneNumber']) ?></strong></span></div>
                            <div><i class="fa-solid fa-location-dot"></i><span><small>Address</small><strong><?= e($request['address']) ?></strong></span></div>
                            <div><i class="fa-solid fa-cake-candles"></i><span><small>Age / Birth year</small><strong><?= (int) $request['age'] ?> · <?= (int) $request['birthYear'] ?></strong></span></div>
                            <div><i class="fa-solid fa-stethoscope"></i><span><small>Specialty</small><strong><?= e($request['speciality']) ?></strong></span></div>
                            <div><i class="fa-solid fa-graduation-cap"></i><span><small>Qualification</small><strong><?= e($request['qualification']) ?></strong></span></div>
                        </div>

                        <form method="post" action="query/approve.php" class="approval-actions js-confirm-form-group">
            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int) $request['id'] ?>">
                            <button type="submit" name="reject" class="approval-reject"
                                data-confirm-title="Reject <?= e($request['fullName']) ?>?"
                                data-confirm-message="This removes the pending registration request. The applicant will not be added to the verified doctor directory."
                                data-confirm-label="Reject request"
                                data-confirm-tone="danger">
                                <i class="fa-solid fa-xmark"></i> Reject
                            </button>
                            <button type="submit" name="approve" class="approval-approve"
                                data-confirm-title="Approve <?= e($request['fullName']) ?>?"
                                data-confirm-message="This moves the applicant into the verified doctor directory and enables their doctor account."
                                data-confirm-label="Approve doctor"
                                data-confirm-tone="success">
                                <i class="fa-solid fa-check"></i> Approve doctor
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>
            <div class="filter-empty-state" id="approval-directory-empty" hidden><i class="fa-solid fa-magnifying-glass"></i><strong>No requests match that search</strong><p>Try a doctor name, specialty, qualification or location.</p></div>
        <?php else: ?>
            <div class="approval-cleared-state">
                <div class="approval-cleared-icon"><i class="fa-solid fa-user-check"></i></div>
                <span>Queue cleared</span>
                <h2>Every doctor request has been reviewed.</h2>
                <p>New provider registrations will appear here automatically for verification.</p>
                <a href="doctor_list.php"><i class="fa-solid fa-user-doctor"></i> View verified doctors</a>
            </div>
        <?php endif; ?>

        <footer class="admin-workspace-footer"><span><i class="fa-solid fa-shield-heart"></i> SmartCare Hub Operations</span><span>Pending approvals: <?= $approvals ?></span></footer>
    </main>
</div>
<?php include 'confirm_modal.php'; ?>
<script src="../../js/admin-workspace.js"></script>
  <script src="../../js/system.js" defer></script>
</body>
</html>
