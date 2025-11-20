<?php
session_start();

// Hapus session
session_destroy();

// Redirect ke login
header("Location: index.php");
exit;
?>
