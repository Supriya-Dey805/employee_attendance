<?php
require_once 'includes/auth.php';
require_role('employee');
$uid = current_user_id();
$month = max(1, min(12, (int)($_GET['month'] ?? date('n'))));
$year = max(2000, min(2100, (int)($_GET['year'] ?? date('Y'))));
$stmt = $conn->prepare('SELECT attendance_date, check_in, check_out FROM attendance WHERE employee_id = ? AND MONTH(attendance_date) = ? AND YEAR(attendance_date) = ? ORDER BY attendance_date DESC');
$stmt->bind_param('iii', $uid, $month, $year);
$stmt->execute();
$rows = $stmt->get_result();
page_header('My attendance');
?>
<div class="page-heading"><div><span class="eyebrow">PERSONAL RECORDS</span><h1>Attendance history</h1><p class="muted">Review your check-in and check-out records.</p></div><a class="button button-outline" href="employee_dashboard.php">← Dashboard</a></div>
<section class="panel">
<form method="get" class="filter-bar">
<label>Month<select name="month"><?php for ($m=1; $m<=12; $m++): ?><option value="<?= $m ?>" <?= $m===$month?'selected':'' ?>><?= e(date('F', mktime(0,0,0,$m,1))) ?></option><?php endfor; ?></select></label>
<label>Year<input type="number" name="year" min="2000" max="2100" value="<?= $year ?>"></label>
<button class="button button-primary" type="submit">Apply filter</button>
</form>
<div class="table-wrap"><table><thead><tr><th>Date</th><th>Day</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr></thead><tbody>
<?php if ($rows->num_rows): while ($row=$rows->fetch_assoc()): ?><tr><td><?= e($row['attendance_date']) ?></td><td><?= e(date('l', strtotime($row['attendance_date']))) ?></td><td><?= e($row['check_in'] ?? '—') ?></td><td><?= e($row['check_out'] ?? '—') ?></td><td><span class="pill <?= get_attendance_status($row['check_in'])==='Late'?'pill-amber':'pill-green' ?>"><?= e(get_attendance_status($row['check_in'])) ?></span></td></tr><?php endwhile; else: ?><tr><td colspan="5" class="empty">No records for this month.</td></tr><?php endif; ?>
</tbody></table></div>
</section>
<?php page_footer(); ?>
