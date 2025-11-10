<?php
// Sisipkan file koneksi
include 'koneksi.php';

// Ambil data untuk dropdown kategori
$kategori_result = $conn->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori");

// Ambil data untuk dropdown status
$status_result = $conn->query("SELECT id_status, nama_status FROM status_barang ORDER BY nama_status");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang Baru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box; /* Agar padding tidak menambah lebar */
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Form Tambah Barang</h2>
        
        <form action="proses_tambah.php" method="POST">
            
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" id="nama_barang" name="nama_barang" required>
            </div>

            <div class="form-group">
                <label for="id_kategori">Kategori:</label>
                <select id="id_kategori" name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                    // Looping data kategori
                    if ($kategori_result->num_rows > 0) {
                        while($row = $kategori_result->fetch_assoc()) {
                            echo '<option value="' . $row['id_kategori'] . '">' . htmlspecialchars($row['nama_kategori']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="harga_sewa">Harga Sewa (Rp):</label>
                <input type="number" id="harga_sewa" name="harga_sewa" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="id_status">Status Barang:</label>
                <select id="id_status" name="id_status" required>
                    <option value="">-- Pilih Status --</option>
                    <?php
                    // Looping data status
                    if ($status_result->num_rows > 0) {
                        while($row = $status_result->fetch_assoc()) {
                            echo '<option value="' . $row['id_status'] . '">' . htmlspecialchars($row['nama_status']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" value="1" min="0" required>
            </div>

            <button type="submit" class="btn-submit">Tambah Barang</button>

        </form>
    </div>

</body>
</html>

<?php
// Tutup koneksi setelah selesai
$conn->close();
?>