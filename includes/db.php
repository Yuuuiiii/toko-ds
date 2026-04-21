<?php
$host = 'localhost';
$user = 'root';
$pass = '2006';
$db   = 'toko_ds';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
	die('Koneksi database gagal: ' . $conn->connect_error);
}
?>
