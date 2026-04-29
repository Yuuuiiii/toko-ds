<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $res = $conn->query("SELECT * FROM supplier ORDER BY ID_Supplier DESC");
    $suppliers = [];
    while($row = $res->fetch_assoc()) { $suppliers[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $suppliers]);
}
elseif ($method === 'POST') {
    $nama = $data['Nama_Supplier'] ?? '';
    $kontak = $data['Kontak_Supplier'] ?? '';
    $sales = $data['Nama_Sales'] ?? '';
    $alamat = $data['Alamat'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO supplier (Nama_Supplier, Kontak_Supplier, Nama_Sales, Alamat) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $kontak, $sales, $alamat);
    if ($stmt->execute()) echo json_encode(['status' => 'success']);
    else echo json_encode(['status' => 'error', 'message' => 'Gagal menambah supplier!']);
}
elseif ($method === 'PUT') {
    $id = $data['ID_Supplier'] ?? '';
    $nama = $data['Nama_Supplier'] ?? '';
    $kontak = $data['Kontak_Supplier'] ?? '';
    $sales = $data['Nama_Sales'] ?? '';
    $alamat = $data['Alamat'] ?? '';
    
    $stmt = $conn->prepare("UPDATE supplier SET Nama_Supplier=?, Kontak_Supplier=?, Nama_Sales=?, Alamat=? WHERE ID_Supplier=?");
    $stmt->bind_param("ssssi", $nama, $kontak, $sales, $alamat, $id);
    if ($stmt->execute()) echo json_encode(['status' => 'success']);
    else echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate supplier!']);
}
elseif ($method === 'DELETE') {
    $id = $data['ID_Supplier'] ?? '';
    
    // Proteksi: Cek apakah supplier ini sudah punya riwayat pembelian/barang masuk
    $check = $conn->query("SELECT ID_Pembelian FROM pembelian WHERE ID_Supplier = $id LIMIT 1");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'GAGAL: Supplier ini memiliki riwayat transaksi/pembelian aktif!']);
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM supplier WHERE ID_Supplier = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) echo json_encode(['status' => 'success']);
    else echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus supplier!']);
}
?>