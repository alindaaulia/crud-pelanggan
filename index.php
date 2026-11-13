<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/config/flash.php';
require __DIR__ . '/config/csrf.php';

$perHalaman = 5;
$cari       = trim($_GET['cari'] ?? '');
$halaman    = max(1, (int) ($_GET['halaman'] ?? 1));

// 1. Hitung total data (sesuai pencarian)
if ($cari !== '') {
    $kata = '%' . $cari . '%';
    $hitung = $pdo->prepare(
        'SELECT COUNT(*) FROM pelanggan WHERE nama LIKE :cari1 OR email LIKE :cari2'
    );
    $hitung->execute([':cari1' => $kata, ':cari2' => $kata]);
} else {
    $hitung = $pdo->query('SELECT COUNT(*) FROM pelanggan');
}
$totalData    = (int) $hitung->fetchColumn();
$totalHalaman = max(1, (int) ceil($totalData / $perHalaman));

// Kalau halaman melebihi batas, kembalikan ke halaman terakhir
if ($halaman > $totalHalaman) {
    $halaman = $totalHalaman;
}
$offset = ($halaman - 1) * $perHalaman;

// 2. Ambil data untuk halaman ini
if ($cari !== '') {
    $stmt = $pdo->prepare(
        'SELECT id, nama, email, telepon, alamat, tanggal_lahir
         FROM pelanggan
         WHERE nama LIKE :cari1 OR email LIKE :cari2
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset'
    );
    $stmt->bindValue(':cari1', $kata);
    $stmt->bindValue(':cari2', $kata);
} else {
    $stmt = $pdo->prepare(
        'SELECT id, nama, email, telepon, alamat, tanggal_lahir
         FROM pelanggan
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset'
    );
}
$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pelanggan = $stmt->fetchAll();

// Helper: buat URL halaman dengan tetap membawa kata kunci
function url_halaman(int $h, string $cari): string
{
    $q = ['halaman' => $h];
    if ($cari !== '') {
        $q['cari'] = $cari;
    }
    return 'index.php?' . http_build_query($q);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pelanggan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h1 class="page-title">Daftar Pelanggan</h1>
        <p class="page-sub">Kelola data pelanggan: tambah, ubah, cari, dan hapus.</p>

        <?php tampilkan_flash(); ?>

        <div class="toolbar">
            <form method="get" class="search-form">
                <input type="text" name="cari" placeholder="Cari nama atau email..."
                       value="<?= htmlspecialchars($cari) ?>">
                <button type="submit" class="btn btn-primary">Cari</button>
                <?php if ($cari !== ''): ?>
                    <a class="link-reset" href="index.php">Reset</a>
                <?php endif; ?>
            </form>
            <a class="btn btn-primary" href="create.php">+ Tambah Pelanggan</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Tanggal Lahir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($pelanggan) === 0): ?>
                    <tr><td colspan="7" class="empty">
                        <?= $cari !== '' ? 'Tidak ada pelanggan yang cocok dengan "' . htmlspecialchars($cari) . '".' : 'Belum ada data pelanggan.' ?>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($pelanggan as $i => $p): ?>
                    <tr>
                        <td><?= $offset + $i + 1 ?></td>
                        <td>
                            <div class="nama-cell">
                                <span class="avatar"><?= htmlspecialchars(strtoupper(mb_substr($p['nama'], 0, 1))) ?></span>
                                <?= htmlspecialchars($p['nama']) ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($p['email']) ?></td>
                        <td><?= $p['telepon'] ? htmlspecialchars($p['telepon']) : '<span class="muted">-</span>' ?></td>
                        <td><?= $p['alamat'] ? htmlspecialchars($p['alamat']) : '<span class="muted">-</span>' ?></td>
                        <td><?= $p['tanggal_lahir'] ? date('d-m-Y', strtotime($p['tanggal_lahir'])) : '<span class="muted">-</span>' ?></td>
                        <td>
                            <div class="aksi">
                                <a class="btn btn-sm btn-edit" href="edit.php?id=<?= $p['id'] ?>">Edit</a>
                                <form method="post" action="delete.php"
                                      onsubmit="return confirm('Yakin ingin menghapus <?= htmlspecialchars($p['nama'], ENT_QUOTES) ?>?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-hapus">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalData > 0): ?>
            <p class="info-data">Menampilkan <?= $offset + 1 ?>-<?= $offset + count($pelanggan) ?> dari <?= $totalData ?> pelanggan</p>
        <?php endif; ?>

        <?php if ($totalHalaman > 1): ?>
        <div class="paging">
            <?php if ($halaman > 1): ?>
                <a href="<?= url_halaman($halaman - 1, $cari) ?>">&laquo; Sebelumnya</a>
            <?php else: ?>
                <span class="nonaktif">&laquo; Sebelumnya</span>
            <?php endif; ?>

            <?php for ($h = 1; $h <= $totalHalaman; $h++): ?>
                <?php if ($h === $halaman): ?>
                    <span class="aktif"><?= $h ?></span>
                <?php else: ?>
                    <a href="<?= url_halaman($h, $cari) ?>"><?= $h ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($halaman < $totalHalaman): ?>
                <a href="<?= url_halaman($halaman + 1, $cari) ?>">Berikutnya &raquo;</a>
            <?php else: ?>
                <span class="nonaktif">Berikutnya &raquo;</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>