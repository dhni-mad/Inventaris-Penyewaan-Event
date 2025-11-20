<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$id = intval($_GET['id'] ?? 0);

if ($id == 0) {
    header("Location: index.php");
    exit;
}

// Ambil data transaksi
$query = "SELECT t.*, u.nama_lengkap FROM transaksi t 
          LEFT JOIN users u ON t.id_user = u.id_user 
          WHERE t.id_transaksi = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$transaksi = $result->fetch_assoc();
$stmt->close();

if (!$transaksi) {
    header("Location: index.php");
    exit;
}

// Ambil detail transaksi
$query = "SELECT dt.*, b.nama_barang FROM detail_transaksi dt 
          LEFT JOIN barang b ON dt.id_barang = b.id_barang 
          WHERE dt.id_transaksi = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$details = [];
while ($row = $result->fetch_assoc()) {
    $details[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Transaksi - Sistem Inventaris Barang</title>
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
        <h1 class="page-title">Detail Transaksi #<?php echo $transaksi['id_transaksi']; ?></h1>

        <div class="form-container">
            <div style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 15px;">Informasi Transaksi</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; font-weight: bold; width: 200px;">Nomor Transaksi</td>
                        <td style="padding: 10px;">#<?php echo $transaksi['id_transaksi']; ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; font-weight: bold;">User</td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($transaksi['nama_lengkap'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; font-weight: bold;">Tanggal Pinjam</td>
                        <td style="padding: 10px;"><?php echo date('d/m/Y', strtotime($transaksi['tanggal_pinjam'])); ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; font-weight: bold;">Tanggal Kembali</td>
                        <td style="padding: 10px;"><?php echo $transaksi['tanggal_kembali'] ? date('d/m/Y', strtotime($transaksi['tanggal_kembali'])) : 'Belum dikembalikan'; ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; font-weight: bold;">Status</td>
                        <td style="padding: 10px;">
                            <span style="padding: 5px 10px; border-radius: 5px; 
                                <?php 
                                if ($transaksi['status_transaksi'] == 'selesai') echo 'background-color: #d4edda; color: #155724;';
                                elseif ($transaksi['status_transaksi'] == 'proses') echo 'background-color: #fff3cd; color: #856404;';
                                elseif ($transaksi['status_transaksi'] == 'batal') echo 'background-color: #f8d7da; color: #721c24;';
                                ?>
                            ">
                                <?php echo ucfirst($transaksi['status_transaksi']); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <div style="margin-bottom: 30px;">
                <h3 style="margin-bottom: 15px;">Detail Barang</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $detail): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detail['nama_barang']); ?></td>
                                <td><?php echo $detail['jumlah']; ?></td>
                                <td>Rp <?php echo number_format($detail['harga_satuan'], 2, ',', '.'); ?></td>
                                <td>Rp <?php echo number_format($detail['subtotal'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                <h3 style="margin-bottom: 10px;">Total Harga: Rp <?php echo number_format($transaksi['total_harga'], 2, ',', '.'); ?></h3>
            </div>

            <div class="form-buttons">
                <a href="edit.php?id=<?php echo $transaksi['id_transaksi']; ?>" class="btn btn-warning">Edit</a>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</body>
</html>
