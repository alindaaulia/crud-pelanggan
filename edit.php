<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/config/flash.php';
require __DIR__ . '/config/csrf.php';

// Ambil dan validasi ID dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    die('ID tidak valid. <a href="index.php">Kembali</a>');
}

// Ambil data pelanggan berdasarkan ID
$stmt = $pdo->prepare(
    'SELECT id, nama, email, telepon, alamat, tanggal_lahir FROM pelanggan WHERE id = :id'
);
$stmt->execute([':id' => $id]);
$pelanggan = $stmt->fetch();

if (!$pelanggan) {
    http_response_code(404);
    die('Pelanggan dengan ID tersebut tidak ditemukan. <a href="index.php">Kembali</a>');
}

$errors        = [];
$nama          = $pelanggan['nama'];
$email         = $pelanggan['email'];
$telepon       = $pelanggan['telepon'] ?? '';
$alamat        = $pelanggan['alamat'] ?? '';
$tanggal_lahir = $pelanggan['tanggal_lahir'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama          = trim($_POST['nama'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $telepon       = trim($_POST['telepon'] ?? '');
    $alamat        = trim($_POST['alamat'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');

    if (!csrf_valid()) {
        http_response_code(403);
        $errors[] = 'Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        // Validasi
        if ($nama === '') {
            $errors[] = 'Nama wajib diisi.';
        }
        if ($email === '') {
            $errors[] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if ($tanggal_lahir !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $tanggal_lahir);
            if (!$d || $d->format('Y-m-d') !== $tanggal_lahir) {
                $errors[] = 'Format tanggal lahir tidak valid.';
            } elseif ($d > new DateTime('today')) {
                $errors[] = 'Tanggal lahir tidak boleh di masa depan.';
            }
        }

        // Cek email duplikat (abaikan milik pelanggan ini sendiri)
        if (empty($errors)) {
            $cek = $pdo->prepare('SELECT COUNT(*) FROM pelanggan WHERE email = :email AND id != :id');
            $cek->execute([':email' => $email, ':id' => $id]);
            if ($cek->fetchColumn() > 0) {
                $errors[] = 'Email sudah dipakai pelanggan lain.';
            }
        }

        // Update
        if (empty($errors)) {
            $upd = $pdo->prepare(
                'UPDATE pelanggan
                 SET nama = :nama, email = :email, telepon = :telepon,
                     alamat = :alamat, tanggal_lahir = :tanggal_lahir
                 WHERE id = :id'
            );
            $upd->execute([
                ':nama'          => $nama,
                ':email'         => $email,
                ':telepon'       => $telepon !== '' ? $telepon : null,
                ':alamat'        => $alamat !== '' ? $alamat : null,
                ':tanggal_lahir' => $tanggal_lahir !== '' ? $tanggal_lahir : null,
                ':id'            => $id,
            ]);
            set_flash('sukses', 'Data pelanggan berhasil diperbarui.');
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pelanggan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container narrow">
    <div class="card">
        <h1 class="page-title">Edit Pelanggan</h1>
        <p class="page-sub">Ubah data pelanggan lalu klik Update.</p>

        <?php if (!empty($errors)): ?>
            <div class="flash flash-gagal">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="form-group">
                <label for="telepon">Telepon <small>(opsional)</small></label>
                <input type="text" id="telepon" name="telepon" value="<?= htmlspecialchars($telepon) ?>">
            </div>

            <div class="form-group">
                <label for="alamat">Alamat <small>(opsional)</small></label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($alamat) ?>">
            </div>

            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir <small>(opsional)</small></label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>">
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Update</button>
                <a class="btn btn-secondary" href="index.php">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>