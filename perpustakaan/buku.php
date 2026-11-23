<?php
include "koneksi.php";

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku = $id");
    header("Location: buku.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $stok = intval($_POST['stok']);
    mysqli_query($koneksi, "INSERT INTO buku (judul, penulis, stok) VALUES ('$judul', '$penulis', $stok)");
    header("Location: buku.php");
    exit;
}

if (isset($_POST['update'])) {
    $id = intval($_POST['id_buku']);
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $stok = intval($_POST['stok']);
    mysqli_query($koneksi, "UPDATE buku SET judul='$judul', penulis='$penulis', stok=$stok WHERE id_buku=$id");
    header("Location: buku.php");
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $q = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = $id");
    if ($q) {
        $edit = mysqli_fetch_assoc($q);
    }
}

$data = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Buku</title>
</head>
<body>
    <h2>Data Buku</h2>
    <p><a href="index.php">Kembali</a></p>

    <?php if (!$data): ?>
        <p style="color:red;">Error: <?= mysqli_error($koneksi) ?></p>
        <p>Tabel <b>buku</b> belum ada. Silakan buat dulu di phpMyAdmin.</p>
    <?php endif; ?>

    <h3><?= $edit ? 'Edit Buku' : 'Tambah Buku' ?></h3>
    <form method="POST">
        <?php if ($edit): ?>
            <input type="hidden" name="id_buku" value="<?= $edit['id_buku'] ?>">
        <?php endif; ?>
        <label>Judul:</label><br>
        <input type="text" name="judul" value="<?= $edit ? htmlspecialchars($edit['judul']) : '' ?>" required><br><br>
        <label>Penulis:</label><br>
        <input type="text" name="penulis" value="<?= $edit ? htmlspecialchars($edit['penulis']) : '' ?>" required><br><br>
        <label>Stok:</label><br>
        <input type="number" name="stok" value="<?= $edit ? $edit['stok'] : 0 ?>" required min="0"><br><br>
        <?php if ($edit): ?>
            <button type="submit" name="update">Update</button>
            <a href="buku.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah</button>
        <?php endif; ?>
    </form>

    <hr>
    <h3>Daftar Buku</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php if ($data): while ($row = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $row['id_buku'] ?></td>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= htmlspecialchars($row['penulis']) ?></td>
            <td><?= $row['stok'] ?></td>
            <td>
                <a href="buku.php?edit=<?= $row['id_buku'] ?>">Edit</a> |
                <a href="buku.php?hapus=<?= $row['id_buku'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; endif; ?>
    </table>
</body>
</html>
