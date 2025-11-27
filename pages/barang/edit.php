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

$query = "SELECT * FROM barang WHERE id_barang = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();
$stmt->close();

if (!$barang) {
    header("Location: index.php");
    exit;
}

$categories = [];
$query = "SELECT * FROM kategori ORDER BY nama_kategori";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$statuses = [];
$query = "SELECT * FROM status_barang ORDER BY nama_status";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $statuses[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = htmlspecialchars($_POST['nama_barang']);
    $id_kategori = intval($_POST['id_kategori']);
    $harga_sewa = floatval($_POST['harga_sewa']);
    $id_status = intval($_POST['id_status']);
    $stok = intval($_POST['stok']);

    if (empty($nama_barang) || $id_kategori == 0 || $harga_sewa == 0 || $id_status == 0) {
        $error = "Semua field harus diisi!";
    } else {
        $query = "UPDATE barang SET nama_barang = ?, id_kategori = ?, harga_sewa = ?, id_status = ?, stok = ? WHERE id_barang = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sidiii", $nama_barang, $id_kategori, $harga_sewa, $id_status, $stok, $id);

        if ($stmt->execute()) {
            $success = "Barang berhasil diperbarui!";
            $barang['nama_barang'] = $nama_barang;
            $barang['id_kategori'] = $id_kategori;
            $barang['harga_sewa'] = $harga_sewa;
            $barang['id_status'] = $id_status;
            $barang['stok'] = $stok;
        } else {
            $error = "Gagal memperbarui barang!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
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

    <div class="container">
        <h1 class="page-title">Edit Barang</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="id_kategori">Kategori</label>
                    <select id="id_kategori" name="id_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id_kategori']; ?>" <?php echo ($barang['id_kategori'] == $cat['id_kategori']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga_sewa">Harga Sewa (Rp)</label>
                    <input type="number" id="harga_sewa" name="harga_sewa" step="0.01" value="<?php echo $barang['harga_sewa']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="id_status">Status</label>
                    <select id="id_status" name="id_status" required>
                        <option value="">-- Pilih Status --</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status['id_status']; ?>" <?php echo ($barang['id_status'] == $status['id_status']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['nama_status']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" min="1" value="<?php echo $barang['stok']; ?>" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
