<?php
require_once 'includes/auth.php';
require_role('admin');
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('employees.php');
verify_csrf();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if ($id) {
    $stmt=$conn->prepare("UPDATE employees SET is_active = IF(is_active=1,0,1) WHERE id=? AND role='employee' AND deleted_at IS NULL");
    $stmt->bind_param('i',$id); $stmt->execute();
    set_flash('Employee account status updated.');
}
redirect('employees.php');
