<?php
include "koneksi.php";

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota = $id");
    header("Location: anggota.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    mysqli_query($koneksi, "INSERT INTO anggota (nama, kelas) VALUES ('$nama', '$kelas')");
    header("Location: anggota.php");
    exit;
}

if (isset($_POST['update'])) {
    $id = intval($_POST['id_anggota']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    mysqli_query($koneksi, "UPDATE anggota SET nama='$nama', kelas='$kelas' WHERE id_anggota=$id");
    header("Location: anggota.php");
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $q = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota = $id");
    if ($q) {
        $edit = mysqli_fetch_assoc($q);
    }
}

$data = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY id_anggota DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota</title>
</head>
<body>
    <h2>Data Anggota</h2>
    <p><a href="index.php">Kembali</a></p>

    <?php if (!$data): ?>
        <p style="color:red;">Error: <?= mysqli_error($koneksi) ?></p>
        <p>Tabel <b>anggota</b> belum ada. Silakan buat dulu di phpMyAdmin.</p>
    <?php endif; ?>

    <h3><?= $edit ? 'Edit Anggota' : 'Tambah Anggota' ?></h3>
    <form method="POST">
        <?php if ($edit): ?>
            <input type="hidden" name="id_anggota" value="<?= $edit['id_anggota'] ?>">
        <?php endif; ?>
        <label>Nama:</label><br>
        <input type="text" name="nama" value="<?= $edit ? htmlspecialchars($edit['nama']) : '' ?>" required><br><br>
        <label>Kelas:</label><br>
        <input type="text" name="kelas" value="<?= $edit ? htmlspecialchars($edit['kelas']) : '' ?>" required><br><br>
        <?php if ($edit): ?>
            <button type="submit" name="update">Update</button>
            <a href="anggota.php">Batal</a>
        <?php else: ?>
            <button type="submit" name="tambah">Tambah</button>
        <?php endif; ?>
    </form>

    <hr>
    <h3>Daftar Anggota</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Aksi</th>
        </tr>
        <?php if ($data): while ($row = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= $row['id_anggota'] ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['kelas']) ?></td>
            <td>
                <a href="anggota.php?edit=<?= $row['id_anggota'] ?>">Edit</a> |
                <a href="anggota.php?hapus=<?= $row['id_anggota'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; endif; ?>
    </table>
</body>
</html>
