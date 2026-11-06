<?php
require_once 'includes/auth.php';
require_role('employee');
set_flash('Use the Check out button on your dashboard to record attendance.', 'warning');
redirect('employee_dashboard.php');
