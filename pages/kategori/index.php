<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $check_query = "SELECT COUNT(*) as total FROM barang WHERE id_kategori = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    $check_stmt->close();
    
    if ($row['total'] > 0) {
        $error = "Kategori tidak bisa dihapus karena masih digunakan oleh barang!";
    } else {
        $delete_query = "DELETE FROM kategori WHERE id_kategori = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            $success = "Kategori berhasil dihapus!";
        } else {
            $error = "Gagal menghapus kategori!";
        }
        $delete_stmt->close();
    }
}

$query = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$result = $conn->query($query);
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <h2>Sistem Inventaris Barang</h2>
        <ul class="navbar-menu">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="index.php" class="active">Kategori</a></li>
            <li><a href="../status/index.php">Status</a></li>
            <li><a href="../barang/index.php">Barang</a></li>
            <li><a href="../transaksi/index.php">Transaksi</a></li>
        </ul>
        <div class="navbar-user">
            <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</span>
            <a href="../../logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title">Kelola Kategori</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="btn-group">
            <a href="add.php" class="btn btn-primary">Tambah Kategori</a>
        </div>

        <?php if (count($categories) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id_kategori']; ?></td>
                            <td><?php echo htmlspecialchars($cat['nama_kategori']); ?></td>
                            <td class="table-actions">
                                <a href="edit.php?id=<?php echo $cat['id_kategori']; ?>" class="btn btn-warning">Edit</a>
                                <a href="index.php?action=delete&id=<?php echo $cat['id_kategori']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada data kategori. <a href="add.php" style="color: #667eea;">Tambah sekarang</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
