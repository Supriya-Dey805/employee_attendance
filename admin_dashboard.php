<?php
require_once 'includes/auth.php';
require_role('admin');
$today = date('Y-m-d');
$counts = $conn->query("SELECT COUNT(*) AS total, SUM(is_active = 1) AS active FROM employees WHERE role = 'employee' AND deleted_at IS NULL")->fetch_assoc();
$stmt = $conn->prepare('SELECT COUNT(*) AS marked, SUM(check_out IS NOT NULL) AS completed FROM attendance WHERE attendance_date = ?');
$stmt->bind_param('s', $today); $stmt->execute();
$todayStats = $stmt->get_result()->fetch_assoc();
$recent = $conn->query("SELECT a.attendance_date, a.check_in, a.check_out, e.name, e.email FROM attendance a JOIN employees e ON e.id=a.employee_id WHERE a.attendance_date='$today' ORDER BY a.check_in DESC LIMIT 8");
page_header('Admin dashboard');
?>
<div class="page-heading"><div><span class="eyebrow">ADMINISTRATION</span><h1>Dashboard overview</h1><p class="muted">Monitor employees and daily attendance activity.</p></div><span class="date-chip"><?= e(date('d M Y')) ?></span></div>
<div class="stats-grid">
<article class="stat-card"><span class="stat-label">Total employees</span><strong><?= (int)$counts['total'] ?></strong><small>All employee accounts</small></article>
<article class="stat-card"><span class="stat-label">Active employees</span><strong><?= (int)$counts['active'] ?></strong><small>Accounts enabled</small></article>
<article class="stat-card"><span class="stat-label">Checked in today</span><strong><?= (int)($todayStats['marked'] ?? 0) ?></strong><small>Attendance entries</small></article>
<article class="stat-card"><span class="stat-label">Checked out today</span><strong><?= (int)($todayStats['completed'] ?? 0) ?></strong><small>Completed records</small></article>
</div>
<div class="admin-layout">
<section class="panel"><div class="panel-heading"><div><h2>Today's activity</h2><p class="muted">Latest attendance records.</p></div><a href="attendance_report.php" class="text-link">Full report →</a></div>
<div class="table-wrap"><table><thead><tr><th>Employee</th><th>Check-in</th><th>Check-out</th></tr></thead><tbody>
<?php if ($recent->num_rows): while($r=$recent->fetch_assoc()): ?><tr><td><b><?= e($r['name']) ?></b><small class="table-sub"><?= e($r['email']) ?></small></td><td><?= e($r['check_in'] ?? '—') ?></td><td><?= e($r['check_out'] ?? '—') ?></td></tr><?php endwhile; else: ?><tr><td colspan="3" class="empty">No attendance records yet today.</td></tr><?php endif; ?>
</tbody></table></div></section>
<aside class="panel quick-panel"><h2>Quick actions</h2><a class="quick-link" href="employees.php"><span>♙</span><div><b>Manage employees</b><small>Add, edit or deactivate accounts</small></div>→</a><a class="quick-link" href="attendance_report.php"><span>▤</span><div><b>Attendance report</b><small>Filter records by date</small></div>→</a><a class="quick-link" href="inactive_employees.php"><span>◷</span><div><b>Inactive employees</b><small>View disabled accounts</small></div>→</a></aside>
</div>
<?php page_footer(); ?>
