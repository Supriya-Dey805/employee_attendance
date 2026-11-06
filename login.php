<?php
require_once 'includes/auth.php';

$role = $_GET['role'] ?? $_POST['role'] ?? '';
if (!in_array($role, ['admin', 'employee'], true)) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, name, email, password_hash, role FROM employees WHERE email = ? AND is_active = 1 AND deleted_at IS NULL LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash']) && $user['role'] === $role) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        set_flash('Welcome back, ' . $user['name'] . '.');
        redirect($role === 'admin' ? 'admin_dashboard.php' : 'employee_dashboard.php');
    }
    set_flash('Invalid email, password or account type.', 'danger');
}
page_header(ucfirst($role) . ' sign in');
?>
<div class="auth-wrap">
    <section class="auth-card">
        <a class="back-link" href="index.php">← Back to home</a>
        <span class="eyebrow"><?= e(strtoupper($role)) ?> ACCESS</span>
        <h1>Welcome back</h1>
        <p class="muted">Sign in to continue to your attendance workspace.</p>
        <form method="post" class="form-stack">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="role" value="<?= e($role) ?>">
            <label>Email address<input type="email" name="email" required autocomplete="username" placeholder="name@company.com"></label>
            <label>Password<input type="password" name="password" required autocomplete="current-password" placeholder="Enter your password"></label>
            <button class="button button-primary button-full" type="submit">Sign in</button>
        </form>
        <p class="switch-role">Signing in as <?= e($role) ?>? <a href="login.php?role=<?= $role === 'admin' ? 'employee' : 'admin' ?>">Switch account type</a></p>
    </section>
</div>
<?php page_footer(); ?>
