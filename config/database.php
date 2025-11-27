<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'db_penyewaan_event';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__FILE__)));
}


try {
    $conn = @new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        die("Koneksi gagal: Terjadi kesalahan saat mencoba koneksi database.");
    }
    
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Error: Terjadi kesalahan tak terduga.");
}
?>
