<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/config/flash.php';
require __DIR__ . '/config/csrf.php';

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Metode tidak diizinkan. <a href="index.php">Kembali</a>');
}

// Cek token CSRF
if (!csrf_valid()) {
    http_response_code(403);
    set_flash('gagal', 'Token keamanan tidak valid. Penghapusan dibatalkan.');
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('gagal', 'ID tidak valid.');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM pelanggan WHERE id = :id');
$stmt->execute([':id' => $id]);

if ($stmt->rowCount() === 0) {
    set_flash('gagal', 'Pelanggan tidak ditemukan.');
} else {
    set_flash('sukses', 'Pelanggan berhasil dihapus.');
}

header('Location: index.php');
exit;