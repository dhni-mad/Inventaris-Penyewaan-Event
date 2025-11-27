<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$error = '';
$success = '';
$id = intval($_GET['id'] ?? 0);

if ($id == 0) {
    header("Location: index.php");
    exit;
}

$query = "SELECT * FROM transaksi WHERE id_transaksi = ?";
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

$query = "SELECT dt.*, b.nama_barang, b.harga_sewa FROM detail_transaksi dt 
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

$users = [];
$query = "SELECT id_user, nama_lengkap FROM users ORDER BY nama_lengkap";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$barangs = [];
$query = "SELECT id_barang, nama_barang, harga_sewa, stok FROM barang ORDER BY nama_barang";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $barangs[] = $row;
}

$old_status = $transaksi['status_transaksi']; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_kembali = htmlspecialchars($_POST['tanggal_kembali']);
    $status_transaksi = htmlspecialchars($_POST['status_transaksi']);
    $new_status = $status_transaksi;

    if (empty($status_transaksi)) {
        $error = "Status transaksi harus dipilih!";
    } else {
        $conn->begin_transaction();
        $transaction_ok = true;
        
        $tanggal_kembali_db = !empty($tanggal_kembali) ? $tanggal_kembali : null;
        
        $query_update_transaksi = "UPDATE transaksi SET tanggal_kembali = ?, status_transaksi = ? WHERE id_transaksi = ?";
        $stmt_update_transaksi = $conn->prepare($query_update_transaksi);
        $stmt_update_transaksi->bind_param("ssi", $tanggal_kembali_db, $new_status, $id);

        if (!$stmt_update_transaksi->execute()) {
            $transaction_ok = false;
            $error = "Gagal memperbarui transaksi!";
        }
        $stmt_update_transaksi->close();
        
        if ($transaction_ok && $old_status == 'proses' && ($new_status == 'selesai' || $new_status == 'batal')) {
            $stock_update_query = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
            $stock_stmt = $conn->prepare($stock_update_query);

            foreach ($details as $detail) {
                $jumlah_dikembalikan = $detail['jumlah'];
                $id_barang = $detail['id_barang'];
                
                $stock_stmt->bind_param("ii", $jumlah_dikembalikan, $id_barang);
                if (!$stock_stmt->execute()) {
                    $transaction_ok = false;
                    $error = "Gagal mengembalikan stok barang! Data transaksi dibatalkan.";
                    break;
                }
            }
            $stock_stmt->close();
        }

        if ($transaction_ok) {
            $conn->commit();
            $success = "Transaksi berhasil diperbarui!";
            $transaksi['tanggal_kembali'] = $tanggal_kembali_db;
            $transaksi['status_transaksi'] = $new_status;
        } else {
            $conn->rollback();
            if (empty($error)) {
                 $error = "Gagal memperbarui transaksi dan mengelola stok. Data tidak disimpan.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
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

    <div class="container">
        <h1 class="page-title">Edit Transaksi #<?php echo $transaksi['id_transaksi']; ?></h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <h3 style="margin-bottom: 20px;">Detail Barang</h3>
            <table class="table" style="margin-bottom: 30px;">
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

            <form method="POST">
                <div class="form-group">
                    <label for="tanggal_kembali">Tanggal Kembali</label>
                    <input type="date" id="tanggal_kembali" name="tanggal_kembali" value="<?php echo $transaksi['tanggal_kembali']; ?>">
                </div>

                <div class="form-group">
                    <label for="status_transaksi">Status Transaksi</label>
                    <select id="status_transaksi" name="status_transaksi" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="proses" <?php echo $transaksi['status_transaksi'] == 'proses' ? 'selected' : ''; ?>>Proses</option>
                        <option value="selesai" <?php echo $transaksi['status_transaksi'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="batal" <?php echo $transaksi['status_transaksi'] == 'batal' ? 'selected' : ''; ?>>Batal</option>
                    </select>
                </div>

                <div style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 30px 0;">
                    <h3>Total Harga: Rp <?php echo number_format($transaksi['total_harga'], 2, ',', '.'); ?></h3>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="view.php?id=<?php echo $transaksi['id_transaksi']; ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
