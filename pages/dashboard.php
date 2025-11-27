<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../config/database.php';

$stats = [];

$query = "SELECT COUNT(*) as total FROM barang";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$stats['total_barang'] = $row['total'];

$query = "SELECT COUNT(*) as total FROM kategori";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$stats['total_kategori'] = $row['total'];

$query = "SELECT COUNT(*) as total FROM transaksi";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$stats['total_transaksi'] = $row['total'];

$query = "SELECT SUM(stok) as total FROM barang";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$stats['total_stok'] = $row['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <h2>Sistem Inventaris Barang</h2>
        <ul class="navbar-menu">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="kategori/index.php">Kategori</a></li>
            <li><a href="status/index.php">Status</a></li>
            <li><a href="barang/index.php">Barang</a></li>
            <li><a href="transaksi/index.php">Transaksi</a></li>
        </ul>
        <div class="navbar-user">
            <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</span>
            <a href="../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Dashboard</h1>

        <div class="dashboard-cards">
            <div class="card card-primary">
                <h3>Total Barang</h3>
                <div class="number"><?php echo $stats['total_barang']; ?></div>
            </div>

            <div class="card card-success">
                <h3>Total Kategori</h3>
                <div class="number"><?php echo $stats['total_kategori']; ?></div>
            </div>

            <div class="card card-warning">
                <h3>Total Stok</h3>
                <div class="number"><?php echo $stats['total_stok']; ?></div>
            </div>

            <div class="card card-danger">
                <h3>Total Transaksi</h3>
                <div class="number"><?php echo $stats['total_transaksi']; ?></div>
            </div>
        </div>

        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #2c3e50; margin-bottom: 20px;">Selamat Datang</h2>
            <p style="color: #666; line-height: 1.6;">
                Anda berada di halaman dashboard Sistem Inventaris dan Pengelolaan Barang. 
                Gunakan menu navigasi di atas untuk mengelola:
            </p>
            <ul style="margin-top: 15px; margin-left: 20px; color: #666; line-height: 1.8;">
                <li><strong>Kategori</strong> - Tambah, ubah, atau hapus kategori barang</li>
                <li><strong>Status</strong> - Kelola status ketersediaan barang</li>
                <li><strong>Barang</strong> - Kelola inventaris barang dengan lengkap</li>
                <li><strong>Transaksi</strong> - Catat transaksi peminjaman dan pengembalian barang</li>
            </ul>
        </div>
    </div>
</body>
</html>
