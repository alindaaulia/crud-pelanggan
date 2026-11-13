<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/config/flash.php';
require __DIR__ . '/config/csrf.php';

$errors        = [];
$nama          = '';
$email         = '';
$telepon       = '';
$alamat        = '';
$tanggal_lahir = '';

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

        // Cek email duplikat
        if (empty($errors)) {
            $cek = $pdo->prepare('SELECT COUNT(*) FROM pelanggan WHERE email = :email');
            $cek->execute([':email' => $email]);
            if ($cek->fetchColumn() > 0) {
                $errors[] = 'Email sudah terdaftar.';
            }
        }

        // Simpan
        if (empty($errors)) {
            $stmt = $pdo->prepare(
                'INSERT INTO pelanggan (nama, email, telepon, alamat, tanggal_lahir)
                 VALUES (:nama, :email, :telepon, :alamat, :tanggal_lahir)'
            );
            $stmt->execute([
                ':nama'          => $nama,
                ':email'         => $email,
                ':telepon'       => $telepon !== '' ? $telepon : null,
                ':alamat'        => $alamat !== '' ? $alamat : null,
                ':tanggal_lahir' => $tanggal_lahir !== '' ? $tanggal_lahir : null,
            ]);
            set_flash('sukses', 'Pelanggan berhasil ditambahkan.');
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
    <title>Tambah Pelanggan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container narrow">
    <div class="card">
        <h1 class="page-title">Tambah Pelanggan</h1>
        <p class="page-sub">Isi data pelanggan baru. Nama dan email wajib diisi.</p>

        <?php if (!empty($errors)): ?>
            <div class="flash flash-gagal">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" onsubmit="return cekOpsional();">
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
                <button class="btn btn-primary" type="submit">Simpan</button>
                <a class="btn btn-secondary" href="index.php">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function cekOpsional() {
    const nama  = document.getElementById('nama');
    const email = document.getElementById('email');

    if (!nama.value.trim()) {
        alert('Nama wajib diisi.');
        nama.focus();
        return false;
    }
    if (!email.value.trim()) {
        alert('Email wajib diisi.');
        email.focus();
        return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        alert('Format email tidak valid.');
        email.focus();
        return false;
    }

    const kosong = [];
    if (!document.getElementById('telepon').value.trim()) kosong.push('Telepon');
    if (!document.getElementById('alamat').value.trim()) kosong.push('Alamat');
    if (!document.getElementById('tanggal_lahir').value) kosong.push('Tanggal Lahir');

    if (kosong.length > 0) {
        return confirm(kosong.join(', ') + ' belum diisi. Tetap simpan?');
    }
    return true;
}
</script>
</body>
</html>