<?php
include 'cek_login.php';

if (!isset($_GET['id'])) {
    die("ID tidak ada");
}

$id = (int) $_GET['id'];

mysqli_query($conn, "DELETE FROM barang_hilang WHERE id=$id");

$_SESSION['flash'] = "Data berhasil dihapus.";
header("Location: index.php");
exit;
?>
