<?php
require_once 'config/database.php';

$check = $conn->query("SELECT COUNT(*) as total FROM users");
$result = $check->fetch_assoc();

if ($result['total'] == 0) {
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $nama_lengkap = 'Administrator';
    $email = 'admin@example.com';

    $query = "INSERT INTO users (username, password, nama_lengkap, email) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $username, $password, $nama_lengkap, $email);

    if ($stmt->execute()) {
        echo "✓ Admin berhasil dibuat!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br><br>";
    } else {
        echo "✗ Gagal membuat admin!<br>";
    }
    $stmt->close();

    $statuses = ['Tersedia', 'Rusak', 'Dipinjam'];
    $query = "INSERT INTO status_barang (nama_status) VALUES (?)";
    $stmt = $conn->prepare($query);

    foreach ($statuses as $status) {
        $stmt->bind_param("s", $status);
        $stmt->execute();
    }
    echo "✓ Status barang berhasil dibuat!<br><br>";
    $stmt->close();

    echo "Setup berhasil! Silahkan <a href='index.php'>login</a>";
} else {
    echo "Database sudah diinisialisasi. <a href='index.php'>Kembali ke login</a>";
}

$conn->close();
?>
