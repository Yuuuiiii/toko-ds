<?php
// Sebaiknya gunakan environment variable / file config terpisah nantinya
$host     = "localhost";
$user     = "root";      
$password = "2006";          
$database = "toko_ds"; 

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Aktifkan exception handling MySQLi

try {
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset("utf8mb4");
    date_default_timezone_set('Asia/Jakarta');
} catch (mysqli_sql_exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Koneksi database gagal',
        'debug' => $e->getMessage() // Sembunyikan ini di production
    ]);
    exit;
}
?>