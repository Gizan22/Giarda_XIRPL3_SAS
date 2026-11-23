<?php
include "koneksi.php";

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $q = mysqli_query($koneksi, "SELECT id_buku, status FROM peminjaman WHERE id_pinjam = $id");
    if ($q) {
        $p = mysqli_fetch_assoc($q);
        if ($p && $p['status'] == 'Dipinjam') {
            mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = {$p['id_buku']}");
        }
    }
    mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_pinjam = $id");
    header("Location: pinjam.php");
    exit;
}

if (isset($_GET['kembali'])) {
    $id = intval($_GET['kembali']);
    $q = mysqli_query($koneksi, "SELECT id_buku, status FROM peminjaman WHERE id_pinjam = $id");
    if ($q) {
        $p = mysqli_fetch_assoc($q);
        if ($p && $p['status'] == 'Dipinjam') {
            $tgl = date('Y-m-d');
            mysqli_query($koneksi, "UPDATE peminjaman SET tanggal_kembali = '$tgl', status = 'Dikembalikan' WHERE id_pinjam = $id");
            mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = {$p['id_buku']}");
        }
    }
    header("Location: pinjam.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $id_anggota = intval($_POST['id_anggota']);
    $id_buku = intval($_POST['id_buku']);
    $tgl = date('Y-m-d');

    $q = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = $id_buku");
    if ($q) {
        $b = mysqli_fetch_assoc($q);
        if ($b && $b['stok'] > 0) {
            mysqli_query($koneksi, "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, status) VALUES ($id_anggota, $id_buku, '$tgl', 'Dipinjam')");
            mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku");
            header("Location: pinjam.php");
            exit;
        } else {
            $error = "Stok buku habis!";
        }
    } else {
        $error = "Gagal cek stok: " . mysqli_error($koneksi);
    }
}

$anggota = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY nama");
$buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul");

$data = mysqli_query($koneksi, "
    SELECT p.*, a.nama, a.kelas, b.judul 
    FROM peminjaman p 
    JOIN anggota a ON p.id_anggota = a.id_anggota 
    JOIN buku b ON p.id_buku = b.id_buku 
    ORDER BY p.id_pinjam DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Peminjaman</title>
</head>
<body>
    <h2>Data Peminjaman</h2>
    <p><a href="index.php">Kembali</a></p>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if (!$anggota || !$buku): ?>
        <p style="color:red;">Error query: <?= mysqli_error($koneksi) ?></p>
        <p>Pastikan tabel <b>anggota</b> dan <b>buku</b> sudah ada dan berisi data.</p>
    <?php endif; ?>

    <h3>Tambah Peminjaman</h3>
    <form method="POST">
        <label>Anggota:</label><br>
        <select name="id_anggota" required>
            <option value="">-- Pilih Anggota --</option>
            <?php if ($anggota): while ($a = mysqli_fetch_assoc($anggota)): ?>
                <option value="<?= $a['id_anggota'] ?>"><?= htmlspecialchars($a['nama']) ?> (<?= htmlspecialchars($a['kelas']) ?>)</option>
            <?php endwhile; endif; ?>
        </select><br><br>
        <label>Buku:</label><br>
        <select name="id_buku" required>
            <option value="">-- Pilih Buku --</option>
            <?php if ($buku): while ($b = mysqli_fetch_assoc($buku)): ?>
                <option value="<?= $b['id_buku'] ?>"><?= htmlspecialchars($b['judul']) ?> (Stok: <?= $b['stok'] ?>)</option>
            <?php endwhile; endif; ?>
        </select><br><br>
        <button type="submit" name="tambah">Pinjamkan</button>
    </form>

    <hr>
    <h3>Daftar Peminjaman</h3>
    <?php if (!$data): ?>
        <p style="color:red;">Error query daftar: <?= mysqli_error($koneksi) ?></p>
    <?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Anggota</th>
            <th>Kelas</th>
            <th>Buku</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $row['id_pinjam'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['kelas']) ?></td>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= $row['tanggal_pinjam'] ?></td>
            <td><?= $row['tanggal_kembali'] ? $row['tanggal_kembali'] : '-' ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if ($row['status'] == 'Dipinjam'): ?>
                    <a href="pinjam.php?kembali=<?= $row['id_pinjam'] ?>" onclick="return confirm('Konfirmasi pengembalian?')">Kembalikan</a> |
                <?php endif; ?>
                <a href="pinjam.php?hapus=<?= $row['id_pinjam'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php endif; ?>
</body>
</html>
