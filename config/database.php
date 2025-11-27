<?php
// Koneksi Database
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'db_penyewaan_event';

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__FILE__)));
}

// File: config/database.php

try {
    // Menekan peringatan/error saat koneksi (hanya untuk penanganan manual)
    $conn = @new mysqli($host, $user, $password, $database);
    
    // Cek koneksi
    if ($conn->connect_error) {
        // Di lingkungan produksi, error ini harus di-log.
        // Tampilkan pesan generik ke pengguna, bukan detail error.
        die("Koneksi gagal: Terjadi kesalahan saat mencoba koneksi database.");
    }
    
    // Set charset
    $conn->set_charset("utf8");
} catch (Exception $e) {
    // Tampilkan pesan generik untuk error tak terduga
    die("Error: Terjadi kesalahan tak terduga.");
}
?>
