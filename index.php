<?php
include 'cek_login.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

$limit = 6;
$searchEsc = mysqli_real_escape_string($conn, $search);

$where = '';
if ($search !== '') {
    $where = "WHERE nama_barang LIKE '%$searchEsc%' OR lokasi LIKE '%$searchEsc%' OR deskripsi LIKE '%$searchEsc%'";
}

$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM barang_hilang $where");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalData = (int) $totalRow['total'];
$totalPages = max(1, (int) ceil($totalData / $limit));

if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $limit;

$data = mysqli_query($conn, "SELECT * FROM barang_hilang $where ORDER BY id DESC LIMIT $limit OFFSET $offset");

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

function urlPage($pageNum, $search) {
    $params = [];
    if ($search !== '') $params['search'] = $search;
    $params['page'] = $pageNum;
    return '?' . http_build_query($params);
}

$startPage = max(1, $page - 2);
$endPage = min($totalPages, $page + 2);
$noteColors = 5;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lost & Found</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="page-wrap">

    <!-- TOPBAR -->
    <div class="topbar">
        <span>Masuk sebagai <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
        <a href="logout.php" class="link-logout">Keluar</a>
    </div>

    <div class="board">

        <!-- HEADER -->
        <div class="board-header">
            <div>
                <div class="title"><span class="title-accent">Lost &amp; Found</span></div>
                <div class="desc">Papan pengumuman barang hilang area kampus</div>
            </div>
            <a href="tambah.php" class="btn-primary">+ Tempel Laporan Baru</a>
        </div>

        <!-- SEARCH -->
        <form method="GET" class="search-bar">
            <span class="search-icon">&#128269;</span>
            <input type="text" name="search" placeholder="Cari nama barang, lokasi, atau deskripsi..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-secondary">Cari</button>
            <?php if ($search !== ''): ?>
                <a href="index.php" class="btn-clear">Reset</a>
            <?php endif; ?>
        </form>

        <!-- NOTE GRID -->
        <?php if (mysqli_num_rows($data) > 0): ?>
            <div class="note-grid">
                <?php $idx = 0; while ($d = mysqli_fetch_assoc($data)): $idx++; $colorClass = 'note-c' . ((($d['id'] - 1) % $noteColors) + 1); ?>
                    <div class="note <?= $colorClass ?>" style="--delay: <?= $idx * 0.06 ?>s">
                        <span class="note-tag">#<?= str_pad($d['id'], 4, '0', STR_PAD_LEFT) ?></span>
                        <h3><?= htmlspecialchars($d['nama_barang']) ?></h3>
                        <p class="note-desc"><?= htmlspecialchars($d['deskripsi']) ?></p>
                        <div class="note-meta">
                            <span class="meta-item">&#128205; <?= htmlspecialchars($d['lokasi']) ?></span>
                            <span class="meta-item">&#128197; <?= htmlspecialchars(date('d M Y', strtotime($d['tanggal']))) ?></span>
                        </div>
                        <div class="note-actions">
                            <a href="edit.php?id=<?= $d['id'] ?>" title="Edit">&#9998;</a>
                            <a href="#" class="js-delete" data-id="<?= $d['id'] ?>" data-name="<?= htmlspecialchars($d['nama_barang'], ENT_QUOTES) ?>" title="Hapus">&#128465;</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <span class="pin-icon">&#128204;</span>
                <?php if ($search !== ''): ?>
                    <p>Belum ada catatan yang cocok dengan "<?= htmlspecialchars($search) ?>".</p>
                    <a href="index.php" class="btn-secondary">Lihat semua catatan</a>
                <?php else: ?>
                    <p>Papan masih kosong, belum ada barang yang dilaporkan.</p>
                    <a href="tambah.php" class="btn-primary">+ Tempel Laporan Pertama</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- PAGINATION -->
        <?php if ($totalData > 0): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= urlPage($page - 1, $search) ?>" class="page-btn">&lsaquo; Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="<?= urlPage($i, $search) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= urlPage($page + 1, $search) ?>" class="page-btn">Selanjutnya &rsaquo;</a>
            <?php endif; ?>

            <span class="page-info">Halaman <?= $page ?> dari <?= $totalPages ?> &middot; <?= $totalData ?> data</span>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal-box">
        <div class="modal-icon">&#128465;&#65039;</div>
        <h3>Hapus catatan ini?</h3>
        <p>Laporan <strong id="modal-item-name"></strong> akan dilepas dari papan dan tidak bisa dikembalikan.</p>
        <div class="modal-actions">
            <button type="button" id="modal-cancel-btn" class="btn-clear">Batal</button>
            <a href="#" id="modal-confirm-btn" class="btn-danger">Ya, Hapus</a>
        </div>
    </div>
</div>

<script>
    // Toast untuk flash message
    <?php if ($flash): ?>
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = '&#9989; <?= htmlspecialchars($flash, ENT_QUOTES) ?>';
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('fade-out'), 2500);
    setTimeout(() => toast.remove(), 2900);
    <?php endif; ?>

    // Modal konfirmasi hapus
    const modal = document.getElementById('delete-modal');
    const modalName = document.getElementById('modal-item-name');
    const modalConfirm = document.getElementById('modal-confirm-btn');

    document.querySelectorAll('.js-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            modalName.textContent = btn.dataset.name;
            modalConfirm.href = 'hapus.php?id=' + btn.dataset.id;
            modal.classList.add('open');
        });
    });

    document.getElementById('modal-cancel-btn').addEventListener('click', function () {
        modal.classList.remove('open');
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) modal.classList.remove('open');
    });
</script>

</body>
</html>
