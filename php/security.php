<?php

declare(strict_types=1);

function secure_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    session_start();
}

function csrf_token(): string
{
    secure_session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_is_valid(?string $token): bool
{
    secure_session_start();
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_valid_csrf(string $redirect): void
{
    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        header('Location: ' . $redirect . (str_contains($redirect, '?') ? '&' : '?') . 'error=' . urlencode('Your session token expired. Please try again.'));
        exit;
    }
}

function regenerate_authenticated_session(): void
{
    secure_session_start();
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verify_password_compatible(mysqli $conn, string $table, int $id, string $plainPassword, string $storedPassword): bool
{
    $allowedTables = ['admin', 'patient', 'doctor'];
    if (!in_array($table, $allowedTables, true)) {
        return false;
    }

    $info = password_get_info($storedPassword);
    if (($info['algoName'] ?? 'unknown') !== 'unknown') {
        if (!password_verify($plainPassword, $storedPassword)) {
            return false;
        }
        if (password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
            upgrade_password_hash($conn, $table, $id, $plainPassword);
        }
        return true;
    }

    // Backward compatibility for older SmartCare databases that stored plaintext.
    if (!hash_equals($storedPassword, $plainPassword)) {
        return false;
    }

    upgrade_password_hash($conn, $table, $id, $plainPassword);
    return true;
}

function upgrade_password_hash(mysqli $conn, string $table, int $id, string $plainPassword): void
{
    $allowedTables = ['admin', 'patient', 'doctor'];
    if (!in_array($table, $allowedTables, true)) {
        return;
    }

    // Avoid truncating a hash if an older database has not yet run the security migration.
    $lengthStmt = $conn->prepare("SELECT CHARACTER_MAXIMUM_LENGTH AS max_length FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'password' LIMIT 1");
    if ($lengthStmt) {
        $lengthStmt->bind_param('s', $table);
        $lengthStmt->execute();
        $column = $lengthStmt->get_result()->fetch_assoc();
        $lengthStmt->close();
        if ((int) ($column['max_length'] ?? 0) < 60) {
            return;
        }
    }

    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE {$table} SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('si', $hash, $id);
        $stmt->execute();
        $stmt->close();
    }
}

function password_matches_existing(string $plainPassword, string $storedPassword): bool
{
    $info = password_get_info($storedPassword);
    return (($info['algoName'] ?? 'unknown') !== 'unknown')
        ? password_verify($plainPassword, $storedPassword)
        : hash_equals($storedPassword, $plainPassword);
}

function require_post_request(string $redirect): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Location: ' . $redirect);
        exit;
    }
}

function clean_string(mixed $value): string
{
    return trim((string) $value);
}
