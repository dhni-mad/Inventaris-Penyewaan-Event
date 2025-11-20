<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$error = '';
$success = '';

// GET - Delete transaksi
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Delete detail_transaksi dulu
    $conn->query("DELETE FROM detail_transaksi WHERE id_transaksi = $id");
    
    // Kemudian delete transaksi
    $query = "DELETE FROM transaksi WHERE id_transaksi = $id";
    if ($conn->query($query)) {
        $success = "Transaksi berhasil dihapus!";
    } else {
        $error = "Gagal menghapus transaksi!";
    }
}

// Ambil semua transaksi
$query = "SELECT t.*, u.nama_lengkap, COUNT(dt.id_detail) as jumlah_item
          FROM transaksi t
          LEFT JOIN users u ON t.id_user = u.id_user
          LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
          GROUP BY t.id_transaksi
          ORDER BY t.id_transaksi DESC";
$result = $conn->query($query);
$transaksis = [];
while ($row = $result->fetch_assoc()) {
    $transaksis[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <h2>Sistem Inventaris Barang</h2>
        <ul class="navbar-menu">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="../kategori/index.php">Kategori</a></li>
            <li><a href="../status/index.php">Status</a></li>
            <li><a href="../barang/index.php">Barang</a></li>
            <li><a href="index.php" class="active">Transaksi</a></li>
        </ul>
        <div class="navbar-user">
            <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</span>
            <a href="../../logout.php">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">Kelola Transaksi</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="btn-group">
            <a href="add.php" class="btn btn-primary">Tambah Transaksi</a>
        </div>

        <?php if (count($transaksis) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Items</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksis as $trans): ?>
                        <tr>
                            <td><?php echo $trans['id_transaksi']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($trans['tanggal_pinjam'])); ?></td>
                            <td><?php echo $trans['tanggal_kembali'] ? date('d/m/Y', strtotime($trans['tanggal_kembali'])) : '-'; ?></td>
                            <td><?php echo $trans['jumlah_item']; ?></td>
                            <td>Rp <?php echo number_format($trans['total_harga'] ?? 0, 2, ',', '.'); ?></td>
                            <td>
                                <span style="padding: 5px 10px; border-radius: 5px; 
                                    <?php 
                                    if ($trans['status_transaksi'] == 'selesai') echo 'background-color: #d4edda; color: #155724;';
                                    elseif ($trans['status_transaksi'] == 'proses') echo 'background-color: #fff3cd; color: #856404;';
                                    elseif ($trans['status_transaksi'] == 'batal') echo 'background-color: #f8d7da; color: #721c24;';
                                    ?>
                                ">
                                    <?php echo ucfirst($trans['status_transaksi']); ?>
                                </span>
                            </td>
                            <td class="table-actions">
                                <a href="view.php?id=<?php echo $trans['id_transaksi']; ?>" class="btn btn-primary">Lihat</a>
                                <a href="edit.php?id=<?php echo $trans['id_transaksi']; ?>" class="btn btn-warning">Edit</a>
                                <a href="index.php?action=delete&id=<?php echo $trans['id_transaksi']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada data transaksi. <a href="add.php" style="color: #667eea;">Buat sekarang</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
