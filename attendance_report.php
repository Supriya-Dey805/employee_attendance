<?php
require_once 'includes/auth.php';
require_role('admin');
$from=$_GET['from']??date('Y-m-01'); $to=$_GET['to']??date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$from)) $from=date('Y-m-01');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$to)) $to=date('Y-m-d');
$stmt=$conn->prepare('SELECT a.attendance_date,a.check_in,a.check_out,e.name,e.email FROM attendance a JOIN employees e ON e.id=a.employee_id WHERE a.attendance_date BETWEEN ? AND ? ORDER BY a.attendance_date DESC,e.name');
$stmt->bind_param('ss',$from,$to); $stmt->execute(); $rows=$stmt->get_result();
page_header('Attendance report');
?>
<div class="page-heading"><div><span class="eyebrow">REPORTING</span><h1>Attendance report</h1><p class="muted">Review recorded attendance for a selected date range.</p></div><a class="button button-outline" href="admin_dashboard.php">← Dashboard</a></div>
<section class="panel"><form method="get" class="filter-bar"><label>From<input type="date" name="from" value="<?= e($from) ?>" required></label><label>To<input type="date" name="to" value="<?= e($to) ?>" required></label><button class="button button-primary">Apply filter</button></form>
<div class="table-wrap"><table><thead><tr><th>Date</th><th>Employee</th><th>Email</th><th>Check-in</th><th>Check-out</th><th>Status</th></tr></thead><tbody>
<?php if($rows->num_rows): while($r=$rows->fetch_assoc()): $status=get_attendance_status($r['check_in']); ?><tr><td><?= e($r['attendance_date']) ?></td><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['check_in']??'—') ?></td><td><?= e($r['check_out']??'—') ?></td><td><span class="pill <?= $status==='Late'?'pill-amber':'pill-green' ?>"><?= e($status) ?></span></td></tr><?php endwhile; else: ?><tr><td colspan="6" class="empty">No records found in this date range.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php page_footer(); ?>
