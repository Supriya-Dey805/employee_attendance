<?php
require_once 'includes/auth.php';
require_role('employee');
set_flash('Use the Check in button on your dashboard to record attendance.', 'warning');
redirect('employee_dashboard.php');
