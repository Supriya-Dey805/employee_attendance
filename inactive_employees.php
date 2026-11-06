<?php
require_once 'includes/auth.php';
require_role('admin');
$rows=$conn->query("SELECT id,name,email,created_at FROM employees WHERE role='employee' AND is_active=0 AND deleted_at IS NULL ORDER BY name");
page_header('Inactive employees');
?>
<div class="page-heading"><div><span class="eyebrow">PEOPLE</span><h1>Inactive employees</h1><p class="muted">Accounts that are currently disabled.</p></div><a class="button button-outline" href="employees.php">← Employee list</a></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead><tbody>
<?php if($rows->num_rows): while($r=$rows->fetch_assoc()): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e(date('d M Y',strtotime($r['created_at']))) ?></td></tr><?php endwhile; else: ?><tr><td colspan="3" class="empty">There are no inactive employee accounts.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php page_footer(); ?>
