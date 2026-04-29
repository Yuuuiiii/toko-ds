<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$userData = verifyJWT(); // Wajib Login
$id_kasir = $userData['id'] ?? $userData['ID_Pengguna'];

// 1. GET: Hitung Total Tunai Sistem Hari Ini
if ($method === 'GET') {
    $tanggal_hari_ini = date('Y-m-d');
    
    // Hanya hitung yang dibayar pakai "Tunai", QRIS/Debit tidak dihitung karena masuk rekening
    $query = "SELECT SUM(Total_Harga - Total_Diskon) as Total_Tunai 
              FROM penjualan 
              WHERE ID_Kasir = ? AND Metode_Pembayaran = 'Tunai' 
              AND DATE(Tanggal_Penjualan) = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $id_kasir, $tanggal_hari_ini);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    
    $total_tunai = $res['Total_Tunai'] ?? 0;
    
    echo json_encode(['status' => 'success', 'data' => ['Total_Tunai_Sistem' => $total_tunai]]);
    exit;
}

// 2. POST: Simpan Data Shift
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $total_sistem = $input['Total_Tunai_Sistem'] ?? 0;
    $total_fisik = $input['Total_Tunai_Fisik'] ?? 0;
    $selisih = $total_fisik - $total_sistem;
    $catatan = $input['Catatan'] ?? '';

    $stmt = $conn->prepare("INSERT INTO shift_kasir (ID_Kasir, Total_Tunai_Sistem, Total_Tunai_Fisik, Selisih, Catatan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iddds", $id_kasir, $total_sistem, $total_fisik, $selisih, $catatan);
    
    if($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Shift berhasil ditutup!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data shift.']);
    }
    exit;
}
?>