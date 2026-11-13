<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil token (dibuat sekali per session)
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Input hidden untuk ditaruh di dalam form
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

// Cek token dari form dengan token di session
function csrf_valid(): bool
{
    $kirim = $_POST['csrf_token'] ?? '';
    return is_string($kirim)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $kirim);
}