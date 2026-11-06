<?php
require_once 'includes/auth.php';
if (is_logged_in()) {
    redirect($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'employee_dashboard.php');
}
page_header('Welcome');
?>
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">EMPLOYEE OPERATIONS</span>
        <h1>Attendance, made <span>simple.</span></h1>
        <p>A clear and reliable way to record daily check-ins, check-outs and attendance reports.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="login.php?role=employee">Employee sign in</a>
            <a class="button button-light" href="login.php?role=admin">Admin sign in</a>
        </div>
        <div class="hero-note">Secure access · Daily records · Monthly reports</div>
    </div>
    <div class="hero-card">
        <div class="mini-label">TODAY'S WORKFLOW</div>
        <div class="workflow-row"><span class="workflow-icon">↗</span><div><b>Check in</b><small>Record your arrival time</small></div><span class="pill pill-green">09:00 AM</span></div>
        <div class="workflow-row"><span class="workflow-icon">↙</span><div><b>Check out</b><small>Record your departure time</small></div><span class="pill pill-blue">06:00 PM</span></div>
        <div class="workflow-row"><span class="workflow-icon">▤</span><div><b>Attendance report</b><small>Review your monthly activity</small></div><span class="pill">View</span></div>
    </div>
</section>
<?php page_footer(); ?>
