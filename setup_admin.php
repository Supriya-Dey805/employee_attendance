<?php
require_once 'includes/auth.php';
$adminCount = (int)$conn->query("SELECT COUNT(*) AS total FROM employees WHERE role='admin'")->fetch_assoc()['total'];
if ($adminCount > 0) {
    http_response_code(403);
    exit('An administrator account already exists. Remove setup_admin.php from the server.');
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $name=trim($_POST['name']??'');
    $email=strtolower(trim($_POST['email']??''));
    $password=$_POST['password']??'';
    if ($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<10) {
        set_flash('Enter a name, valid email and password with at least 10 characters.','danger');
    } else {
        $hash=password_hash($password,PASSWORD_DEFAULT);
        $stmt=$conn->prepare("INSERT INTO employees(name,email,password_hash,role) VALUES(?,?,?,'admin')");
        $stmt->bind_param('sss',$name,$email,$hash);
        $stmt->execute();
        set_flash('Administrator account created. Sign in to continue.');
        redirect('login.php?role=admin');
    }
}
page_header('Create administrator');
?>
<div class="auth-wrap"><section class="auth-card"><span class="eyebrow">FIRST-TIME SETUP</span><h1>Create admin account</h1><p class="muted">This page is available only until the first admin account is created. Delete this file from the server after setup.</p>
<form method="post" class="form-stack"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Full name<input name="name" required maxlength="120"></label>
<label>Email address<input type="email" name="email" required maxlength="190"></label>
<label>Password<input type="password" name="password" required minlength="10" autocomplete="new-password"></label>
<button class="button button-primary button-full">Create administrator</button></form></section></div>
<?php page_footer(); ?>
