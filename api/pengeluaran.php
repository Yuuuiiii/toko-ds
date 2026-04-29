<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $res = $conn->query("SELECT * FROM pengeluaran ORDER BY Tanggal_Pengeluaran DESC, Waktu_Input DESC");
    $pengeluaran = [];
    while($row = $res->fetch_assoc()) { 
        $pengeluaran[] = $row; 
    }
    echo json_encode(['status' => 'success', 'data' => $pengeluaran]);
}
elseif ($method === 'POST') {
    $tanggal = !empty($data['Tanggal']) ? $data['Tanggal'] : date('Y-m-d');
    $keterangan = $data['Keterangan'] ?? '';
    $nominal = isset($data['Nominal']) ? floatval($data['Nominal']) : 0;

    $stmt = $conn->prepare("INSERT INTO pengeluaran (Tanggal_Pengeluaran, Keterangan, Nominal) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Error DB: ' . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("ssd", $tanggal, $keterangan, $nominal);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Pengeluaran berhasil dicatat!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mencatat pengeluaran.']);
    }
}
?>