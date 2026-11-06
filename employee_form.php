<?php
require_once 'includes/auth.php';
require_role('admin');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$employee = ['name'=>'','email'=>''];
if ($id) {
    $stmt=$conn->prepare("SELECT name,email FROM employees WHERE id=? AND role='employee' AND deleted_at IS NULL");
    $stmt->bind_param('i',$id); $stmt->execute();
    $found=$stmt->get_result()->fetch_assoc();
    if (!$found) { set_flash('Employee not found.','danger'); redirect('employees.php'); }
    $employee=$found;
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
    if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || (!$id && strlen($password)<8) || ($password!=='' && strlen($password)<8)) {
        set_flash('Enter a name, valid email and a password of at least 8 characters.','danger');
    } else {
        try {
            if ($id && $password==='') {
                $stmt=$conn->prepare("UPDATE employees SET name=?,email=? WHERE id=? AND role='employee'");
                $stmt->bind_param('ssi',$name,$email,$id);
            } elseif ($id) {
                $hash=password_hash($password,PASSWORD_DEFAULT);
                $stmt=$conn->prepare("UPDATE employees SET name=?,email=?,password_hash=? WHERE id=? AND role='employee'");
                $stmt->bind_param('sssi',$name,$email,$hash,$id);
            } else {
                $hash=password_hash($password,PASSWORD_DEFAULT);
                $stmt=$conn->prepare("INSERT INTO employees(name,email,password_hash,role) VALUES(?,?,?,'employee')");
                $stmt->bind_param('sss',$name,$email,$hash);
            }
            $stmt->execute();
            set_flash($id?'Employee details updated.':'Employee account created.');
            redirect('employees.php');
        } catch (mysqli_sql_exception $e) {
            set_flash('That email address is already in use.','danger');
        }
    }
}
page_header($id?'Edit employee':'Add employee');
?>
<div class="page-heading"><div><span class="eyebrow">PEOPLE</span><h1><?= $id?'Edit employee':'Add employee' ?></h1><p class="muted">Employee account details.</p></div><a class="button button-outline" href="employees.php">← Back</a></div>
<section class="panel form-panel"><form method="post" class="form-stack">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Full name<input name="name" maxlength="120" required value="<?= e($employee['name']) ?>" placeholder="Employee name"></label>
<label>Email address<input type="email" name="email" maxlength="190" required value="<?= e($employee['email']) ?>" placeholder="name@company.com"></label>
<label>Password <span class="muted"><?= $id?'(leave blank to keep current password)':'(minimum 8 characters)' ?></span><input type="password" name="password" <?= $id?'':'required' ?> minlength="8" autocomplete="new-password"></label>
<button class="button button-primary" type="submit"><?= $id?'Save changes':'Create employee' ?></button>
</form></section>
<?php page_footer(); ?>
