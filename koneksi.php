<?php
// Konfigurasi database
$servername = "localhost"; // Ganti jika server Anda berbeda
$username = "root";        // Ganti dengan username database Anda
$password = "";            // Ganti dengan password database Anda
$dbname = "db_penyewaan_event"; // Nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengatur charset ke utf8 (opsional namun disarankan)
$conn->set_charset("utf8");
?>