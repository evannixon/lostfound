<?php
// Include file ini di paling atas setiap halaman yang WAJIB login dulu.
// Otomatis menyiapkan koneksi database ($conn) dan mengecek session.
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
