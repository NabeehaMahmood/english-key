<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        // Only mark the cookie Secure when actually served over HTTPS, so
        // local XAMPP testing over plain HTTP keeps working.
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireAdmin(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function attemptLogin(string $username, string $password): bool
{
    // Simple brute-force throttle: after 5 failed attempts in this session,
    // refuse further tries for 60 seconds.
    $failed = $_SESSION['login_failures'] ?? 0;
    $lastFail = $_SESSION['login_last_failure'] ?? 0;
    if ($failed >= 5 && (time() - $lastFail) < 60) {
        return false;
    }

    $stmt = getDb()->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $username;
        unset($_SESSION['login_failures'], $_SESSION['login_last_failure']);
        return true;
    }

    $_SESSION['login_failures'] = $failed + 1;
    $_SESSION['login_last_failure'] = time();
    return false;
}

function logoutAdmin(): void
{
    $_SESSION = [];
    session_destroy();
}

// --- CSRF protection ---

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken()) . '">';
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Invalid or expired form submission. Please go back and try again.');
    }
}

// --- Lightweight spam protection for public forms (honeypot + arithmetic check) ---

function humanCheckQuestion(): string
{
    $a = random_int(2, 9);
    $b = random_int(2, 9);
    $_SESSION['human_check_answer'] = $a + $b;
    return "$a + $b";
}

function humanCheckPassed(): bool
{
    $expected = $_SESSION['human_check_answer'] ?? null;
    unset($_SESSION['human_check_answer']);
    return $expected !== null && (int)($_POST['human'] ?? -1) === (int)$expected;
}

function honeypotTripped(): bool
{
    return !empty($_POST['website']);
}

// --- Arithmetic captcha (Alumni "Share Your Story" form). Same lazy-generate
// / one-time-unset session idiom as humanCheck* above, kept separate so it
// isn't tied to Contact's simpler human-check question. ---

/**
 * Generates a random arithmetic captcha with a varying operator (not just
 * fixed addition), stores the expected answer server-side in the session,
 * and returns the question text to display. Called on every page render
 * and again on every refresh-captcha request, so a fresh question/answer
 * pair always backs whatever is currently on screen.
 */
function enrollCaptchaQuestion(): string
{
    $operators = ['+', '-', '×'];
    $operator = $operators[array_rand($operators)];

    if ($operator === '-') {
        $a = random_int(6, 15);
        $b = random_int(2, $a - 1); // keep the result non-negative
        $answer = $a - $b;
    } elseif ($operator === '×') {
        $a = random_int(2, 9);
        $b = random_int(2, 9);
        $answer = $a * $b;
    } else {
        $a = random_int(2, 12);
        $b = random_int(2, 12);
        $answer = $a + $b;
    }

    $_SESSION['enroll_captcha_answer'] = $answer;
    return "$a $operator $b";
}

function enrollCaptchaPassed(): bool
{
    $expected = $_SESSION['enroll_captcha_answer'] ?? null;
    unset($_SESSION['enroll_captcha_answer']);
    return $expected !== null && (int)($_POST['captcha_answer'] ?? -1) === (int)$expected;
}

