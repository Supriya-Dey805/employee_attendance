<?php
require_once 'includes/auth.php';
require_role('admin');
redirect('attendance_report.php');
