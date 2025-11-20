<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$error = '';
$success = '';

// GET - Ambil data barang
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Cek apakah barang digunakan di detail transaksi
    $check = $conn->query("SELECT COUNT(*) as total FROM detail_transaksi WHERE id_barang = $id");
    $result = $check->fetch_assoc();
    
    if ($result['total'] > 0) {
        $error = "Barang tidak bisa dihapus karena sudah digunakan dalam transaksi!";
    } else {
        $query = "DELETE FROM barang WHERE id_barang = $id";
        if ($conn->query($query)) {
            $success = "Barang berhasil dihapus!";
        } else {
            $error = "Gagal menghapus barang!";
        }
    }
}

// Ambil semua barang dengan kategori dan status
$query = "SELECT b.*, k.nama_kategori, s.nama_status 
          FROM barang b
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
          LEFT JOIN status_barang s ON b.id_status = s.id_status
          ORDER BY b.id_barang DESC";
$result = $conn->query($query);
$barangs = [];
while ($row = $result->fetch_assoc()) {
    $barangs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang - Sistem Inventaris Barang</title>
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
            <li><a href="index.php" class="active">Barang</a></li>
            <li><a href="../transaksi/index.php">Transaksi</a></li>
        </ul>
        <div class="navbar-user">
            <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</span>
            <a href="../../logout.php">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">Kelola Barang</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="btn-group">
            <a href="add.php" class="btn btn-primary">Tambah Barang</a>
        </div>

        <?php if (count($barangs) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga Sewa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barangs as $barang): ?>
                        <tr>
                            <td><?php echo $barang['id_barang']; ?></td>
                            <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($barang['nama_kategori'] ?? '-'); ?></td>
                            <td><?php echo $barang['stok']; ?></td>
                            <td>Rp <?php echo number_format($barang['harga_sewa'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($barang['nama_status'] ?? '-'); ?></td>
                            <td class="table-actions">
                                <a href="edit.php?id=<?php echo $barang['id_barang']; ?>" class="btn btn-warning">Edit</a>
                                <a href="index.php?action=delete&id=<?php echo $barang['id_barang']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada data barang. <a href="add.php" style="color: #667eea;">Tambah sekarang</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
