<?php
require_once 'includes/auth.php';
require_role('employee');
$uid = current_user_id();
$today = date('Y-m-d');
$stmt = $conn->prepare('SELECT check_in, check_out FROM attendance WHERE employee_id = ? AND attendance_date = ?');
$stmt->bind_param('is', $uid, $today);
$stmt->execute();
$todayRow = $stmt->get_result()->fetch_assoc();
$month = date('m'); $year = date('Y');
$stmt = $conn->prepare("SELECT COUNT(*) AS days, SUM(check_in IS NOT NULL) AS present_days FROM attendance WHERE employee_id = ? AND MONTH(attendance_date) = ? AND YEAR(attendance_date) = ?");
$stmt->bind_param('iii', $uid, $month, $year);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
page_header('Employee dashboard');
?>
<div class="page-heading"><div><span class="eyebrow">EMPLOYEE WORKSPACE</span><h1>Hello, <?= e($_SESSION['name']) ?>.</h1><p class="muted">Here is your attendance overview for <?= e(date('l, d F Y')) ?>.</p></div><span class="date-chip"><?= e(date('d M Y')) ?></span></div>
<div class="stats-grid">
    <article class="stat-card"><span class="stat-label">This month's records</span><strong><?= (int)($stats['days'] ?? 0) ?></strong><small>Attendance entries</small></article>
    <article class="stat-card"><span class="stat-label">Days present</span><strong><?= (int)($stats['present_days'] ?? 0) ?></strong><small>Current month</small></article>
    <article class="stat-card"><span class="stat-label">Today's status</span><strong class="stat-word"><?= $todayRow ? e(get_attendance_status($todayRow['check_in'])) : 'Not marked' ?></strong><small><?= $todayRow ? 'Attendance recorded' : 'No check-in yet' ?></small></article>
</div>
<section class="panel">
    <div class="panel-heading"><div><h2>Today's attendance</h2><p class="muted">Standard schedule: check-in 09:00–09:30 and check-out 18:00–18:30 (IST).</p></div><span class="pill pill-green">Live record</span></div>
    <div class="time-grid">
        <div class="time-box"><span>CHECK-IN</span><strong><?= e($todayRow['check_in'] ?? '--:--:--') ?></strong></div>
        <div class="time-box"><span>CHECK-OUT</span><strong><?= e($todayRow['check_out'] ?? '--:--:--') ?></strong></div>
    </div>
    <div class="action-row">
        <form method="post" action="mark_attendance.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="check_in">
            <button class="button button-primary" type="submit" <?= ($todayRow && $todayRow['check_in']) ? 'disabled' : '' ?>>Check in</button>
        </form>
        <form method="post" action="mark_attendance.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="check_out">
            <button class="button button-light" type="submit" <?= (!$todayRow || !$todayRow['check_in'] || $todayRow['check_out']) ? 'disabled' : '' ?>>Check out</button>
        </form>
        <a class="button button-outline" href="my_attendance.php">View attendance history</a>
    </div>
</section>
<?php page_footer(); ?>
