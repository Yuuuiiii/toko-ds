<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'GET') {
	echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
	exit;
}

// Contoh: total pendapatan, kas tunai, non-tunai, selisih kas, tren penjualan, distribusi metode bayar, riwayat transaksi
$periode = $_GET['periode'] ?? 'hari';

// TODO: Query sesuai kebutuhan frontend (dummy data dulu)
$response = [
	'total_pendapatan' => 1000000,
	'kas_tunai' => 600000,
	'kas_nontunai' => 400000,
	'selisih_kas' => 0,
	'tren_penjualan' => [
		'labels' => ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'],
		'data' => [150000, 450000, 320000, 890000, 540000, 760000, 1100000]
	],
	'metode_bayar' => [
		'labels' => ['Tunai', 'QRIS', 'Debit'],
		'data' => [65, 25, 10]
	],
	'riwayat' => [
		[
			'waktu' => '2025-01-02 08:15',
			'kasir' => 'Dewi Lestari',
			'tipe' => 'Tunai',
			'ref' => '—',
			'nominal' => 38000,
			'status' => 'Selesai'
		]
	]
];
echo json_encode(['status' => 'success', 'data' => $response]);
exit;
?>
