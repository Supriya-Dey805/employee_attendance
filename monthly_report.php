<?php
require_once 'includes/auth.php';
require_role('admin');
$start = date('Y-m-01');
$end = date('Y-m-t');
redirect('attendance_report.php?from=' . $start . '&to=' . $end);
