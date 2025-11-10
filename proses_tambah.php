<?php
// Sisipkan file koneksi
include 'koneksi.php';

// Cek apakah data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form dan bersihkan (basic security)
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = (int)$_POST['id_kategori']; // Casting ke integer
    $harga_sewa = (float)$_POST['harga_sewa']; // Casting ke float/double
    $id_status = (int)$_POST['id_status'];     // Casting ke integer
    $stok = (int)$_POST['stok'];               // Casting ke integer

    // Validasi sederhana (pastikan yang required tidak kosong)
    if (empty($nama_barang) || empty($id_kategori) || empty($harga_sewa) || empty($id_status) || $stok < 0) {
        echo "Error: Semua field wajib diisi dan stok tidak boleh negatif.";
    } else {
        
        // --- Menggunakan Prepared Statements (PENTING untuk keamanan) ---
        
        // 1. Siapkan SQL query dengan placeholder (?)
        $sql = "INSERT INTO barang (nama_barang, id_kategori, harga_sewa, id_status, stok) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        // 2. Bind parameter ke placeholder
        // "sidis" adalah tipe data:
        // s = string (nama_barang)
        // i = integer (id_kategori)
        // d = double (harga_sewa)
        // i = integer (id_status)
        // i = integer (stok)
        $stmt->bind_param("sidii", $nama_barang, $id_kategori, $harga_sewa, $id_status, $stok);

        // 3. Eksekusi statement
        if ($stmt->execute()) {
            echo "<h2>Sukses!</h2>";
            echo "Barang baru berhasil ditambahkan ke database.";
            echo "<br><br>";
            echo '<a href="tambah_barang.php">Tambah Barang Lagi</a>';
            echo " | ";
            echo '<a href="http://localhost/phpmyadmin/sql.php?server=1&db=db_penyewaan_event&table=barang&pos=0" target="_blank">Cek di phpMyAdmin</a>'; // Link langsung ke tabel
        } else {
            echo "<h2>Error!</h2>";
            echo "Gagal menambahkan barang: " . $stmt->error;
        }

        // 4. Tutup statement
        $stmt->close();
    }
    
    // Tutup koneksi
    $conn->close();

} else {
    // Jika file diakses langsung tanpa POST data
    echo "Akses tidak sah. Silakan isi form terlebih dahulu.";
    echo '<br><a href="tambah_barang.php">Kembali ke Form</a>';
}
?>