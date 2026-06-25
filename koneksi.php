<?php
session_start();

$conn = mysqli_connect("sql302.infinityfree.com", "if0_42264459", "nixon053", "if0_42264459_lostfound");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>