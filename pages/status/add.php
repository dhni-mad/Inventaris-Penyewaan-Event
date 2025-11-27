<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_status = htmlspecialchars($_POST['nama_status']);

    if (empty($nama_status)) {
        $error = "Nama status harus diisi!";
    } else {
        $query = "INSERT INTO status_barang (nama_status) VALUES (?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nama_status);

        if ($stmt->execute()) {
            $success = "Status berhasil ditambahkan!";
            $nama_status = '';
        } else {
            $error = "Gagal menambahkan status!";
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
    <title>Tambah Status - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <h2>Sistem Inventaris Barang</h2>
        <ul class="navbar-menu">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="../kategori/index.php">Kategori</a></li>
            <li><a href="index.php" class="active">Status</a></li>
            <li><a href="../barang/index.php">Barang</a></li>
            <li><a href="../transaksi/index.php">Transaksi</a></li>
        </ul>
        <div class="navbar-user">
            <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</span>
            <a href="../../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Tambah Status</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="nama_status">Nama Status</label>
                    <input type="text" id="nama_status" name="nama_status" placeholder="Contoh: Tersedia, Rusak, Dipinjam" value="<?php echo htmlspecialchars($nama_status ?? ''); ?>" required>
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
