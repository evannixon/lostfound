<?php
include 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        $usernameEsc = mysqli_real_escape_string($conn, $username);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$usernameEsc'");

        if (mysqli_num_rows($check) > 0) {
            $error = "Username sudah dipakai, coba username lain.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$usernameEsc', '$hash')");
            $success = "Akun berhasil dibuat. Silakan masuk.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
</head>
<body class="auth-page">

<div class="auth-wrap">
    <div class="brand">
        <span class="pin-badge"></span>
        <span class="brand-name">Lost &amp; Found</span>
    </div>

    <div class="note-card note-c4">
        <h1>Daftar Petugas</h1>
        <p class="note-sub">Buat akun untuk mulai mengelola data barang hilang.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required autofocus>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Konfirmasi Password</label>
            <input type="password" name="confirm" required>

            <button type="submit" class="btn-primary btn-block">Daftar</button>
        </form>

        <p class="note-foot">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
</div>

</body>
</html>
