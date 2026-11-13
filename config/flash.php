<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simpan pesan ($tipe: 'sukses' atau 'gagal')
function set_flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'pesan' => $pesan];
}

// Tampilkan pesan lalu hapus (hanya muncul sekali)
function tampilkan_flash(): void
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);

    $kelas = $f['tipe'] === 'sukses' ? 'flash-sukses' : 'flash-gagal';
    echo '<div class="flash ' . $kelas . '">' . htmlspecialchars($f['pesan']) . '</div>';
}