<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
	// TODO: Query transaksi, sementara dummy
	$data = [
		[
			'id' => 1,
			'waktu' => '2025-01-02 08:15',
			'kasir' => 'Dewi Lestari',
			'tipe' => 'Tunai',
			'ref' => '—',
			'nominal' => 38000,
			'status' => 'Selesai'
		]
	];
	echo json_encode(['status' => 'success', 'data' => $data]);
	exit;
}

if ($method === 'POST') {
	// TODO: Simpan transaksi baru
	$input = json_decode(file_get_contents('php://input'), true);
	// ...
	echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan (dummy)']);
	exit;
}

echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
exit;
?>
