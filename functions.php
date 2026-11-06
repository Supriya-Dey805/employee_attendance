<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id'], $_SESSION['role']);
}

function require_login(): void {
    if (!is_logged_in()) {
        set_flash('Please sign in to continue.', 'warning');
        redirect('login.php');
    }
}

function require_role(string $role): void {
    require_login();

    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('You do not have permission to view this page.');
    }

    // Re-check employee account status on every protected request.
    if ($role === 'employee') {
        global $conn;

        $userId = current_user_id();

        if (!$conn) {
            http_response_code(500);
            exit('Unable to verify employee account.');
        }

        $stmt = $conn->prepare(
            "SELECT is_active, deleted_at
             FROM employees
             WHERE id = ?
             AND role = 'employee'
             LIMIT 1"
        );

        if (!$stmt) {
            http_response_code(500);
            exit('Unable to verify employee account.');
        }

        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $account = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        // Block missing, inactive, or deleted employee accounts.
        if (
            !$account ||
            (int)$account['is_active'] !== 1 ||
            $account['deleted_at'] !== null
        ) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();

            header('Location: login.php?role=employee&disabled=1');
            exit;
        }
    }
}

function set_flash(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flash(): ?array {
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';

    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Invalid form token. Go back, refresh the page and try again.');
    }
}

function current_user_id(): int {
    return (int)($_SESSION['user_id'] ?? 0);
}

function page_header(string $title): void {
    $flash = get_flash();
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> | <?= e(APP_NAME) ?></title>
        <link rel="stylesheet" href="assets/style.css?v=2">
    </head>
    <body>
    <header class="topbar">
        <a class="brand" href="<?= is_logged_in() ? ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'employee_dashboard.php') : 'index.php' ?>">
            <span class="brand-mark">W</span>
            <span>WorkTrack</span>
        </a>

        <?php if (is_logged_in()): ?>
            <nav class="topnav">
                <span class="nav-user"><?= e($_SESSION['name'] ?? 'User') ?></span>
                <a href="logout.php">Sign out</a>
            </nav>
        <?php endif; ?>
    </header>

    <main class="container">

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?php
}

function page_footer(): void {
    ?>
    </main>
    <footer class="footer">
        WorkTrack Attendance Management System
    </footer>
    </body>
    </html>
    <?php
}

function get_attendance_status(?string $checkIn): string {
    if (!$checkIn) {
        return 'Absent';
    }

    return ($checkIn > '09:30:00') ? 'Late' : 'Present';
}
