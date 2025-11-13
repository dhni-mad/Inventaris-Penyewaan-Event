<?php

include 'koneksi.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    $nama_barang = $_POST['nama_barang'];
    $id_kategori = (int)$_POST['id_kategori']; 
    $harga_sewa = (float)$_POST['harga_sewa']; 
    $id_status = (int)$_POST['id_status'];     
    $stok = (int)$_POST['stok'];              

    
    if (empty($nama_barang) || empty($id_kategori) || empty($harga_sewa) || empty($id_status) || $stok < 0) {
        echo "Error: Semua field wajib diisi dan stok tidak boleh negatif.";
    } else {
        
       
        
        
        $sql = "INSERT INTO barang (nama_barang, id_kategori, harga_sewa, id_status, stok) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        
        $stmt->bind_param("sidii", $nama_barang, $id_kategori, $harga_sewa, $id_status, $stok);

        
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

        
        $stmt->close();
    }
    
  
    $conn->close();

} else {
    
    echo "Akses tidak sah. Silakan isi form terlebih dahulu.";
    echo '<br><a href="tambah_barang.php">Kembali ke Form</a>';
}
?>