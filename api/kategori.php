<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    $res = $conn->query("SELECT * FROM kategori ORDER BY ID_Kategori DESC");
    $kategori = [];
    while($row = $res->fetch_assoc()) { $kategori[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $kategori]);
}
elseif ($method === 'POST') {
    $nama = $data['Nama_Kategori'] ?? '';
    $stmt = $conn->prepare("INSERT INTO kategori (Nama_Kategori) VALUES (?)");
    $stmt->bind_param("s", $nama);
    if ($stmt->execute()) echo json_encode(['status' => 'success']);
    else echo json_encode(['status' => 'error', 'message' => 'Gagal menambah kategori!']);
}
elseif ($method === 'PUT') {
    $id = $data['ID_Kategori'] ?? '';
    $nama = $data['Nama_Kategori'] ?? '';
    $stmt = $conn->prepare("UPDATE kategori SET Nama_Kategori = ? WHERE ID_Kategori = ?");
    $stmt->bind_param("si", $nama, $id);
    if ($stmt->execute()) echo json_encode(['status' => 'success']);
    else echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate kategori!']);
}
elseif ($method === 'DELETE') {
    $id = $data['ID_Kategori'] ?? '';
    // Proteksi: Jangan izinkan hapus jika kategori masih dipakai di tabel barang
    $check = $conn->query("SELECT SKU_Barang FROM barang WHERE ID_Kategori = $id LIMIT 1");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'GAGAL: Kategori ini masih digunakan pada data Master Barang!']);
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM kategori WHERE ID_Kategori = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) echo json_encode(['status' => 'success']);
    else echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kategori!']);
}
?>