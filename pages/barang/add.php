<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$error = '';
$success = '';

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
        $query = "INSERT INTO barang (nama_barang, id_kategori, harga_sewa, id_status, stok) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sidii", $nama_barang, $id_kategori, $harga_sewa, $id_status, $stok);

        if ($stmt->execute()) {
            $success = "Barang berhasil ditambahkan!";
            $nama_barang = '';
            $id_kategori = 0;
            $harga_sewa = 0;
            $id_status = 0;
            $stok = 1;
        } else {
            $error = "Gagal menambahkan barang!";
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
    <title>Tambah Barang - Sistem Inventaris Barang</title>
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
        <h1 class="page-title">Tambah Barang</h1>

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
                    <input type="text" id="nama_barang" name="nama_barang" placeholder="Contoh: Kursi Tamu Premium" value="<?php echo htmlspecialchars($nama_barang ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="id_kategori">Kategori</label>
                    <select id="id_kategori" name="id_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id_kategori']; ?>" <?php echo (isset($id_kategori) && $id_kategori == $cat['id_kategori']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga_sewa">Harga Sewa (Rp)</label>
                    <input type="number" id="harga_sewa" name="harga_sewa" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars($harga_sewa ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="id_status">Status</label>
                    <select id="id_status" name="id_status" required>
                        <option value="">-- Pilih Status --</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status['id_status']; ?>" <?php echo (isset($id_status) && $id_status == $status['id_status']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['nama_status']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" min="1" value="<?php echo htmlspecialchars($stok ?? '1'); ?>" required>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
