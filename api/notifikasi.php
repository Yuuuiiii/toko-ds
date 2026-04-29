<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

// Sesuaikan dengan file koneksi database-mu
require_once '../includes/db.php'; 

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        // 1. Hitung jumlah notifikasi yang belum dibaca
        $resultCount = $conn->query("SELECT COUNT(*) as unread FROM notifikasi_stok WHERE Is_Read = 0");
        $unread = $resultCount->fetch_assoc()['unread'];

        // 2. Ambil 5 notifikasi terbaru
        $resultList = $conn->query("SELECT * FROM notifikasi_stok ORDER BY Tanggal_Notifikasi DESC LIMIT 5");
        
        $list = [];
        if ($resultList) {
            while($row = $resultList->fetch_assoc()) {
                $list[] = $row;
            }
        }

        echo json_encode([
            'status' => 'success',
            'unread' => $unread,
            'data' => $list
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} 
// Method POST digunakan untuk menandai semua pesan "Sudah Dibaca"
elseif ($method === 'POST') {
    try {
        $conn->query("UPDATE notifikasi_stok SET Is_Read = 1 WHERE Is_Read = 0");
        echo json_encode(['status' => 'success', 'message' => 'Semua notifikasi ditandai sudah dibaca']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>