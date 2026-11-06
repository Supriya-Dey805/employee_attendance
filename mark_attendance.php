<?php
require_once 'includes/auth.php';
require_role('employee');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('employee_dashboard.php');
verify_csrf();

$action = $_POST['action'] ?? '';
$uid = current_user_id();
$date = date('Y-m-d');
$time = date('H:i:s');

if ($action === 'check_in') {
    if ($time < '09:00:00' || $time > '09:30:00') {
        set_flash('Check-in is available between 09:00 AM and 09:30 AM IST.', 'warning');
        redirect('employee_dashboard.php');
    }
    try {
        $stmt = $conn->prepare('INSERT INTO attendance (employee_id, attendance_date, check_in) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $uid, $date, $time);
        $stmt->execute();
        set_flash('Check-in recorded successfully.');
    } catch (mysqli_sql_exception $e) {
        set_flash('A check-in record already exists for today.', 'warning');
    }
} elseif ($action === 'check_out') {
    if ($time < '18:00:00' || $time > '18:30:00') {
        set_flash('Check-out is available between 06:00 PM and 06:30 PM IST.', 'warning');
        redirect('employee_dashboard.php');
    }
    $stmt = $conn->prepare('UPDATE attendance SET check_out = ? WHERE employee_id = ? AND attendance_date = ? AND check_in IS NOT NULL AND check_out IS NULL');
    $stmt->bind_param('sis', $time, $uid, $date);
    $stmt->execute();
    set_flash($stmt->affected_rows ? 'Check-out recorded successfully.' : 'No open check-in was found for today.', $stmt->affected_rows ? 'success' : 'warning');
} else {
    set_flash('Unknown attendance action.', 'danger');
}
redirect('employee_dashboard.php');
