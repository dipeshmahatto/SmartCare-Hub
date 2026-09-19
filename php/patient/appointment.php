<?php
require_once '../security.php';
secure_session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: patient_login.php");
    exit;
}

include("query/session_values.php");
include("../database.php");
include("query/date.php");
include("query/image.php");

$patientActivePage = 'appointment';
$pageTitle = 'Book Appointment';
$pageEyebrow = 'Patient Portal';
$errorMessage = isset($_GET['error']) ? trim($_GET['error']) : '';

$doctorRows = [];
$specialtyCounts = [];
$doctorResult = mysqli_query($conn, "SELECT id, fullName, speciality, qualification, address, gender FROM doctor ORDER BY speciality ASC, fullName ASC");
if ($doctorResult) {
    while ($doctor = $doctorResult->fetch_assoc()) {
        $doctorRows[] = $doctor;
        $specialty = trim($doctor['speciality']);
        if ($specialty !== '') {
            $specialtyCounts[$specialty] = ($specialtyCounts[$specialty] ?? 0) + 1;
        }
    }
}

function bookingDoctorAvatar($doctorId)
{
    foreach (['jpg', 'jpeg', 'png'] as $extension) {
        $file = __DIR__ . '/../doctor/uploads/' . $doctorId . '.' . $extension;
        if (file_exists($file)) {
            return '../doctor/uploads/' . $doctorId . '.' . $extension;
        }
    }
    return '';
}

function bookingDoctorInitials($name)
{
    $cleanName = trim(str_ireplace(['Dr.', 'Dr '], '', $name));
    $parts = preg_split('/\s+/', $cleanName);
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
    }
    return $initials ?: 'DR';
}

function specialtyIcon($specialty)
{
    $map = [
        'Surgery' => 'fa-user-doctor',
        'Dental' => 'fa-tooth',
        'Ophthalmology' => 'fa-eye',
        'Radiology' => 'fa-x-ray',
        'Gynoclogist' => 'fa-person-pregnant',
        'Gynecologist' => 'fa-person-pregnant',
    ];
    return $map[$specialty] ?? 'fa-stethoscope';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/patient_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>Book Appointment | SmartCare</title>
</head>
<body>
    <div class="container patient-shell">
        <?php include("sidebar.php"); ?>

        <main class="content patient-content">
            <?php include("top.php"); ?>

            <section class="patient-welcome booking-welcome booking-welcome--wizard">
                <div>
                    <span class="eyebrow">Guided scheduling</span>
                    <h1>Book care in a few simple steps.</h1>
                    <p>Choose the right specialty, doctor, weekday and an available time. Review everything, then send the request to the doctor for confirmation.</p>
                </div>
                <a class="secondary-action" href="patient_dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to dashboard</a>
            </section>

            <?php if ($errorMessage !== ''): ?>
                <div class="alert alert--error"><i class="fa-solid fa-circle-exclamation"></i><span><?= htmlspecialchars($errorMessage) ?></span></div>
            <?php endif; ?>

            <?php if (empty($doctorRows)): ?>
                <section class="booking-empty-state">
                    <div class="empty-state__icon"><i class="fa-solid fa-user-doctor"></i></div>
                    <h2>No doctors are available yet</h2>
                    <p>An administrator needs to approve at least one doctor before appointments can be booked.</p>
                    <a class="secondary-action" href="patient_dashboard.php">Return to dashboard</a>
                </section>
            <?php else: ?>
                <section class="booking-wizard-shell" data-booking-wizard>
                    <div class="booking-progress-wrap" aria-label="Booking progress">
                        <div class="booking-progress-line"><span id="booking-progress-fill"></span></div>
                        <ol class="booking-progress" id="booking-progress">
                            <li class="is-active" data-progress-step="1"><span>1</span><div><strong>Specialty</strong><small>Choose care</small></div></li>
                            <li data-progress-step="2"><span>2</span><div><strong>Doctor</strong><small>Pick specialist</small></div></li>
                            <li data-progress-step="3"><span>3</span><div><strong>Day</strong><small>Select weekday</small></div></li>
                            <li data-progress-step="4"><span>4</span><div><strong>Time</strong><small>Choose slot</small></div></li>
                            <li data-progress-step="5"><span>5</span><div><strong>Review</strong><small>Confirm details</small></div></li>
                        </ol>
                    </div>

                    <div class="booking-workspace">
                        <form action="query/appointment_process.php" method="post" id="booking-wizard-form" class="booking-wizard-form" novalidate>
            <?= csrf_field() ?>
                            <input type="hidden" name="category" id="booking-category" value="">
                            <input type="hidden" name="doctor" id="booking-doctor" value="">
                            <input type="hidden" name="day" id="booking-day" value="">
                            <input type="hidden" name="time" id="booking-time" value="">

                            <section class="booking-panel is-active" data-panel="1" aria-labelledby="step-specialty-title">
                                <div class="booking-panel-heading">
                                    <div><span class="step-kicker">Step 1 of 5</span><h2 id="step-specialty-title">What kind of care do you need?</h2><p>Select a specialty to see the doctors currently available in SmartCare.</p></div>
                                    <div class="panel-symbol"><i class="fa-solid fa-stethoscope"></i></div>
                                </div>

                                <div class="specialty-grid" id="specialty-grid">
                                    <?php foreach ($specialtyCounts as $specialty => $count): ?>
                                        <button type="button" class="specialty-choice" data-specialty="<?= htmlspecialchars($specialty, ENT_QUOTES) ?>">
                                            <span class="specialty-choice__icon"><i class="fa-solid <?= htmlspecialchars(specialtyIcon($specialty)) ?>"></i></span>
                                            <span class="specialty-choice__copy"><strong><?= htmlspecialchars($specialty) ?></strong><small><?= $count ?> <?= $count === 1 ? 'doctor' : 'doctors' ?> available</small></span>
                                            <span class="choice-check"><i class="fa-solid fa-check"></i></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <div class="wizard-actions wizard-actions--end">
                                    <button class="wizard-next" type="button" data-next disabled>Continue to doctors <i class="fa-solid fa-arrow-right"></i></button>
                                </div>
                            </section>

                            <section class="booking-panel" data-panel="2" aria-labelledby="step-doctor-title" hidden>
                                <div class="booking-panel-heading">
                                    <div><span class="step-kicker">Step 2 of 5</span><h2 id="step-doctor-title">Choose your doctor</h2><p>These specialists match the care category you selected.</p></div>
                                    <span class="selection-chip" id="doctor-specialty-chip">Specialty</span>
                                </div>

                                <div class="doctor-choice-grid" id="doctor-choice-grid">
                                    <?php foreach ($doctorRows as $doctor): ?>
                                        <?php $avatar = bookingDoctorAvatar($doctor['id']); ?>
                                        <button
                                            type="button"
                                            class="doctor-choice"
                                            data-doctor-name="<?= htmlspecialchars($doctor['fullName'], ENT_QUOTES) ?>"
                                            data-specialty="<?= htmlspecialchars($doctor['speciality'], ENT_QUOTES) ?>"
                                            data-qualification="<?= htmlspecialchars($doctor['qualification'], ENT_QUOTES) ?>"
                                            data-address="<?= htmlspecialchars($doctor['address'], ENT_QUOTES) ?>"
                                            hidden
                                        >
                                            <span class="doctor-choice__visual">
                                                <?php if ($avatar): ?>
                                                    <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($doctor['fullName']) ?>">
                                                <?php else: ?>
                                                    <span class="doctor-choice__fallback"><?= htmlspecialchars(bookingDoctorInitials($doctor['fullName'])) ?></span>
                                                <?php endif; ?>
                                                <span class="doctor-online-dot" title="Available for booking"></span>
                                            </span>
                                            <span class="doctor-choice__copy">
                                                <span class="doctor-specialty-label"><?= htmlspecialchars($doctor['speciality']) ?></span>
                                                <strong><?= htmlspecialchars($doctor['fullName']) ?></strong>
                                                <small><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars($doctor['qualification']) ?></small>
                                                <small><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($doctor['address']) ?></small>
                                            </span>
                                            <span class="choice-check"><i class="fa-solid fa-check"></i></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                                <div class="booking-inline-empty" id="doctor-empty" hidden><i class="fa-solid fa-user-doctor"></i><div><strong>No doctor found</strong><span>Choose another specialty to continue.</span></div></div>

                                <div class="wizard-actions">
                                    <button class="wizard-back" type="button" data-back><i class="fa-solid fa-arrow-left"></i> Back</button>
                                    <button class="wizard-next" type="button" data-next disabled>Choose a day <i class="fa-solid fa-arrow-right"></i></button>
                                </div>
                            </section>

                            <section class="booking-panel" data-panel="3" aria-labelledby="step-day-title" hidden>
                                <div class="booking-panel-heading">
                                    <div><span class="step-kicker">Step 3 of 5</span><h2 id="step-day-title">Which weekday works for you?</h2><p>SmartCare currently schedules recurring weekday slots rather than calendar dates.</p></div>
                                    <div class="panel-symbol panel-symbol--calendar"><i class="fa-regular fa-calendar-days"></i></div>
                                </div>

                                <div class="weekday-grid" id="weekday-grid">
                                    <?php foreach ($days as $index => $day): ?>
                                        <button type="button" class="weekday-choice" data-day="<?= htmlspecialchars($day) ?>">
                                            <span class="weekday-choice__number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                                            <strong><?= htmlspecialchars(ucfirst(strtolower($day))) ?></strong>
                                            <small><?= $day === $currentDay ? 'Today' : 'Available day' ?></small>
                                            <?php if ($day === $currentDay): ?><span class="today-badge">Today</span><?php endif; ?>
                                            <span class="choice-check"><i class="fa-solid fa-check"></i></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>

                                <div class="wizard-actions">
                                    <button class="wizard-back" type="button" data-back><i class="fa-solid fa-arrow-left"></i> Back</button>
                                    <button class="wizard-next" type="button" data-next disabled>See available times <i class="fa-solid fa-arrow-right"></i></button>
                                </div>
                            </section>

                            <section class="booking-panel" data-panel="4" aria-labelledby="step-time-title" hidden>
                                <div class="booking-panel-heading">
                                    <div><span class="step-kicker">Step 4 of 5</span><h2 id="step-time-title">Choose an available time</h2><p>Booked slots are removed automatically for your selected doctor and weekday.</p></div>
                                    <span class="live-status"><i></i> Live availability</span>
                                </div>

                                <div class="selected-context" id="time-context">
                                    <span><i class="fa-solid fa-user-doctor"></i><strong id="time-context-doctor">Doctor</strong></span>
                                    <span><i class="fa-regular fa-calendar"></i><strong id="time-context-day">Day</strong></span>
                                </div>

                                <div class="time-state" id="time-loading" hidden>
                                    <span class="booking-spinner"></span><div><strong>Checking availability</strong><small>Finding open times for this doctor…</small></div>
                                </div>
                                <div class="time-slot-grid" id="time-slot-grid"></div>
                                <div class="booking-inline-empty" id="time-empty" hidden><i class="fa-regular fa-clock"></i><div><strong>No open times for this weekday</strong><span>Go back and choose another day.</span></div></div>
                                <div class="booking-inline-error" id="time-error" hidden><i class="fa-solid fa-triangle-exclamation"></i><div><strong>Couldn’t load availability</strong><span>Please retry or choose another day.</span></div><button type="button" id="retry-times">Retry</button></div>

                                <div class="wizard-actions">
                                    <button class="wizard-back" type="button" data-back><i class="fa-solid fa-arrow-left"></i> Back</button>
                                    <button class="wizard-next" type="button" data-next disabled>Review appointment <i class="fa-solid fa-arrow-right"></i></button>
                                </div>
                            </section>

                            <section class="booking-panel" data-panel="5" aria-labelledby="step-review-title" hidden>
                                <div class="booking-panel-heading">
                                    <div><span class="step-kicker">Step 5 of 5</span><h2 id="step-review-title">Review your appointment</h2><p>Make sure everything looks right before the request is sent to your doctor.</p></div>
                                    <div class="panel-symbol panel-symbol--success"><i class="fa-solid fa-shield-heart"></i></div>
                                </div>

                                <div class="booking-review-card">
                                    <div class="booking-review-banner">
                                        <span class="review-icon"><i class="fa-solid fa-calendar-check"></i></span>
                                        <div><span>Ready to request</span><h3>Your SmartCare appointment</h3></div>
                                    </div>
                                    <div class="review-doctor-row">
                                        <div class="review-doctor-avatar" id="review-doctor-avatar">DR</div>
                                        <div><span id="review-specialty">Specialty</span><h3 id="review-doctor">Doctor</h3><p id="review-doctor-meta">Qualification</p></div>
                                    </div>
                                    <div class="review-facts">
                                        <div><span><i class="fa-regular fa-calendar"></i> Weekday</span><strong id="review-day">—</strong></div>
                                        <div><span><i class="fa-regular fa-clock"></i> Time</span><strong id="review-time">—</strong></div>
                                        <div><span><i class="fa-solid fa-user"></i> Patient</span><strong><?= htmlspecialchars($fullName) ?></strong></div>
                                    </div>
                                    <div class="review-notice"><i class="fa-solid fa-circle-info"></i><p>New bookings start as Pending. Your doctor confirms the request before it becomes a confirmed appointment, and you can cancel while it is pending or confirmed.</p></div>
                                </div>

                                <div class="wizard-actions wizard-actions--review">
                                    <button class="wizard-back" type="button" data-back><i class="fa-solid fa-pen"></i> Edit details</button>
                                    <button class="wizard-confirm" type="submit" id="booking-confirm"><span>Send appointment request</span><i class="fa-solid fa-arrow-right"></i></button>
                                </div>
                            </section>
                        </form>

                        <aside class="booking-live-summary" aria-label="Booking summary">
                            <div class="summary-brand">
                                <span><i class="fa-solid fa-heart-pulse"></i></span>
                                <div><strong>SmartCare booking</strong><small>Your choices update here</small></div>
                            </div>

                            <div class="summary-completion">
                                <div><span>Booking progress</span><strong id="summary-progress-text">0%</strong></div>
                                <div class="summary-progress-track"><span id="summary-progress-fill"></span></div>
                            </div>

                            <dl class="booking-summary-list">
                                <div data-summary-row="category"><dt><span><i class="fa-solid fa-stethoscope"></i></span> Specialty</dt><dd id="summary-category">Not selected</dd></div>
                                <div data-summary-row="doctor"><dt><span><i class="fa-solid fa-user-doctor"></i></span> Doctor</dt><dd id="summary-doctor">Not selected</dd></div>
                                <div data-summary-row="day"><dt><span><i class="fa-regular fa-calendar"></i></span> Day</dt><dd id="summary-day">Not selected</dd></div>
                                <div data-summary-row="time"><dt><span><i class="fa-regular fa-clock"></i></span> Time</dt><dd id="summary-time">Not selected</dd></div>
                            </dl>

                            <div class="summary-trust-card">
                                <span><i class="fa-solid fa-shield-heart"></i></span>
                                <div><strong>Review before saving</strong><p>Nothing is submitted until you send the request on the final step. The doctor confirms it afterward.</p></div>
                            </div>
                        </aside>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <?php if (!empty($doctorRows)): ?>
        <script src="../../js/booking.js"></script>
    <?php endif; ?>
  <script src="../../js/system.js" defer></script>
</body>
</html>
