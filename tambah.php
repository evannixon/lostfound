<?php
include 'cek_login.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

    mysqli_query($conn, "INSERT INTO barang_hilang (nama_barang, deskripsi, lokasi, tanggal)
        VALUES ('$nama', '$deskripsi', '$lokasi', '$tanggal')");

    $_SESSION['flash'] = "Catatan baru berhasil ditempel.";
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data - Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
</head>
<body class="form-page">

<div class="topbar">
    <a href="index.php" class="link-back">&larr; Kembali ke papan</a>
</div>

<div class="note-card" style="max-width:380px">
    <h2>Tempel Laporan Baru</h2>
    <p class="note-sub">Isi detail barang yang ditemukan atau hilang.</p>

    <form method="POST">
        <label>Nama Barang</label>
        <input type="text" name="nama" placeholder="Contoh: Dompet kulit hitam" required autofocus>

        <label>Deskripsi</label>
        <input type="text" name="deskripsi" placeholder="Ciri-ciri barang" required>

        <label>Lokasi</label>
        <input type="text" name="lokasi" placeholder="Contoh: Perpustakaan Lt. 2" required>

        <label>Tanggal</label>
        <input type="date" name="tanggal" required>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="index.php" class="btn-clear">Batal</a>
        </div>
    </form>
</div>

</body>
</html>
