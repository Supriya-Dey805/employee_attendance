<?php
require_once 'includes/auth.php';
require_role('admin');
$rows = $conn->query("SELECT id, name, email, is_active, created_at FROM employees WHERE role='employee' AND deleted_at IS NULL ORDER BY name");
page_header('Manage employees');
?>
<div class="page-heading"><div><span class="eyebrow">PEOPLE</span><h1>Employee management</h1><p class="muted">Create and maintain employee accounts.</p></div><a class="button button-primary" href="employee_form.php">+ Add employee</a></div>
<section class="panel"><div class="table-wrap"><table><thead><tr><th>Employee</th><th>Email</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if($rows->num_rows): while($r=$rows->fetch_assoc()): ?><tr><td><b><?= e($r['name']) ?></b></td><td><?= e($r['email']) ?></td><td><?= e(date('d M Y',strtotime($r['created_at']))) ?></td><td><span class="pill <?= $r['is_active']?'pill-green':'pill-amber' ?>"><?= $r['is_active']?'Active':'Inactive' ?></span></td><td class="actions"><a href="employee_form.php?id=<?= (int)$r['id'] ?>">Edit</a><form method="post" action="toggle_employee.php" onsubmit="return confirm('Change this employee account status?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="link-button" type="submit"><?= $r['is_active']?'Deactivate':'Activate' ?></button></form></td></tr><?php endwhile; else: ?><tr><td colspan="5" class="empty">No employees added yet.</td></tr><?php endif; ?>
</tbody></table></div></section>
<?php page_footer(); ?>
