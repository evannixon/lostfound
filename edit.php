<?php
include 'cek_login.php';

if (!isset($_GET['id'])) {
    die("ID tidak ada");
}

$id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM barang_hilang WHERE id=$id");
$d = mysqli_fetch_assoc($result);

if (!$d) {
    $_SESSION['flash'] = "Data tidak ditemukan.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

    mysqli_query($conn, "UPDATE barang_hilang SET
        nama_barang='$nama',
        deskripsi='$deskripsi',
        lokasi='$lokasi',
        tanggal='$tanggal'
        WHERE id=$id");

    $_SESSION['flash'] = "Catatan berhasil diperbarui.";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data - Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
</head>
<body class="form-page">

<div class="topbar">
    <a href="index.php" class="link-back">&larr; Kembali ke papan</a>
</div>

<div class="note-card note-c3" style="max-width:380px">
    <h2>Edit Catatan</h2>
    <p class="note-sub">Memperbarui tiket #<?= str_pad($d['id'], 4, '0', STR_PAD_LEFT) ?></p>

    <form method="POST">
        <label>Nama Barang</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($d['nama_barang']) ?>" required autofocus>

        <label>Deskripsi</label>
        <input type="text" name="deskripsi" value="<?= htmlspecialchars($d['deskripsi']) ?>" required>

        <label>Lokasi</label>
        <input type="text" name="lokasi" value="<?= htmlspecialchars($d['lokasi']) ?>" required>

        <label>Tanggal</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($d['tanggal']) ?>" required>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Update</button>
            <a href="index.php" class="btn-clear">Batal</a>
        </div>
    </form>
</div>

</body>
</html>
