<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
	// Ambil semua barang
	$result = $conn->query('SELECT * FROM barang');
	$data = [];
	while ($row = $result->fetch_assoc()) {
		$data[] = $row;
	}
	echo json_encode(['status' => 'success', 'data' => $data]);
	exit;
}

if ($method === 'POST') {
	$input = json_decode(file_get_contents('php://input'), true);
	$sku = $input['SKU_Barang'] ?? '';
	$nama = $input['Nama_Barang'] ?? '';
	$id_kat = $input['ID_Kategori'] ?? null;
	$id_satuan = $input['ID_Satuan_Dasar'] ?? null;
	$harga = $input['Harga_Jual'] ?? 0;
	$stok_min = $input['Stok_Minimum'] ?? 10;
	$stmt = $conn->prepare('INSERT INTO barang (SKU_Barang, Nama_Barang, ID_Kategori, ID_Satuan_Dasar, Harga_Jual, Stok_Minimum) VALUES (?, ?, ?, ?, ?, ?)');
	$stmt->bind_param('ssiddi', $sku, $nama, $id_kat, $id_satuan, $harga, $stok_min);
	if ($stmt->execute()) {
		echo json_encode(['status' => 'success']);
	} else {
		echo json_encode(['status' => 'error', 'message' => $stmt->error]);
	}
	exit;
}

if ($method === 'PUT') {
	$input = json_decode(file_get_contents('php://input'), true);
	$sku = $input['SKU_Barang'] ?? '';
	$nama = $input['Nama_Barang'] ?? '';
	$id_kat = $input['ID_Kategori'] ?? null;
	$id_satuan = $input['ID_Satuan_Dasar'] ?? null;
	$harga = $input['Harga_Jual'] ?? 0;
	$stok_min = $input['Stok_Minimum'] ?? 10;
	$stmt = $conn->prepare('UPDATE barang SET Nama_Barang=?, ID_Kategori=?, ID_Satuan_Dasar=?, Harga_Jual=?, Stok_Minimum=? WHERE SKU_Barang=?');
	$stmt->bind_param('siidis', $nama, $id_kat, $id_satuan, $harga, $stok_min, $sku);
	if ($stmt->execute()) {
		echo json_encode(['status' => 'success']);
	} else {
		echo json_encode(['status' => 'error', 'message' => $stmt->error]);
	}
	exit;
}

if ($method === 'DELETE') {
	$input = json_decode(file_get_contents('php://input'), true);
	$sku = $input['SKU_Barang'] ?? '';
	$stmt = $conn->prepare('DELETE FROM barang WHERE SKU_Barang=?');
	$stmt->bind_param('s', $sku);
	if ($stmt->execute()) {
		echo json_encode(['status' => 'success']);
	} else {
		echo json_encode(['status' => 'error', 'message' => $stmt->error]);
	}
	exit;
}

echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
exit;
?>
