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

try {
    $conn = new mysqli($host, $user, $password, $database);
    
    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
