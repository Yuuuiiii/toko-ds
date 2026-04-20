<?php
require_once 'includes/config.php';

// Memulai atau melajutkan sesi yang ada
session_start();

// Menghapus semua variabel sesi
session_unset();

// Menghancurkan sesi secara fisik di server
session_destroy();

// Paksa browser menghapus cache halaman sebelumnya (mencegah user menekan tombol 'Back')
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Tendang kembali ke halaman login
header("Location: " . BASE_URL . "/index.php");
exit;
?>