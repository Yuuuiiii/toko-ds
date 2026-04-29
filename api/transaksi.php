<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ==========================================
// GATEWAY: PROTEKSI API
// ==========================================
$userData = verifyJWT();

// 1. GET: Ambil riwayat transaksi
if ($method === 'GET') {
    $query = "
        SELECT p.ID_Penjualan, p.Tanggal_Penjualan, pg.Nama_Lengkap AS Kasir, 
               p.Metode_Pembayaran, p.Total_Harga 
        FROM penjualan p
        JOIN pengguna pg ON p.ID_Kasir = pg.ID_Pengguna
        ORDER BY p.Tanggal_Penjualan DESC LIMIT 50
    ";
    
    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// 2. POST: Simpan Transaksi Baru
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['ID_Kasir']) || empty($input['Items']) || !is_array($input['Items'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Payload data tidak lengkap']);
        exit;
    }

    $id_kasir = $userData['id'] ?? $input['ID_Kasir']; 
    $metode_bayar = $input['Metode_Pembayaran'] ?? 'Tunai';
    $total_harga = $input['Total_Harga'] ?? 0;
    
    // TANGKAP DATA MEMBER & DISKON
    $id_pelanggan = !empty($input['ID_Pelanggan']) ? (int)$input['ID_Pelanggan'] : null;
    $total_diskon = !empty($input['Total_Diskon']) ? (float)$input['Total_Diskon'] : 0;
    
    $items = $input['Items'];

    $conn->begin_transaction();

    try {
        // Insert Header Penjualan (Tambah Pelanggan & Diskon)
        $stmtPenjualan = $conn->prepare("INSERT INTO penjualan (ID_Kasir, ID_Pelanggan, Total_Harga, Total_Diskon, Metode_Pembayaran) VALUES (?, ?, ?, ?, ?)");
        $stmtPenjualan->bind_param("iidds", $id_kasir, $id_pelanggan, $total_harga, $total_diskon, $metode_bayar);
        
        if (!$stmtPenjualan->execute()) {
            throw new Exception("Gagal menyimpan data penjualan utama.");
        }
        $id_penjualan = $conn->insert_id;

        $stmtCekSatuan = $conn->prepare("SELECT ID_Satuan_Dasar FROM barang WHERE SKU_Barang = ?");
        $stmtKonversi = $conn->prepare("SELECT Nilai_Konversi FROM konversi_satuan WHERE SKU_Barang = ? AND ID_Satuan_Besar = ? AND ID_Satuan_Kecil = ?");
        $stmtDetail = $conn->prepare("INSERT INTO detail_penjualan (ID_Penjualan, SKU_Barang, Jumlah_Jual, ID_Satuan_Jual, Harga_Saat_Jual) VALUES (?, ?, ?, ?, ?)");
        $stmtPotongStok = $conn->prepare("UPDATE stok_barang SET Jumlah_Stok = Jumlah_Stok - ? WHERE SKU_Barang = ? AND Jumlah_Stok >= ?");

        foreach ($items as $item) {
            $sku = $item['SKU_Barang'];
            $jumlah_jual = $item['Jumlah_Jual'];
            $id_satuan_jual = $item['ID_Satuan_Jual'];
            $harga_jual = $item['Harga_Saat_Jual'];
            
            // Cek Satuan Dasar
            $stmtCekSatuan->bind_param("s", $sku);
            $stmtCekSatuan->execute();
            $resSatuan = $stmtCekSatuan->get_result();
            if ($resSatuan->num_rows === 0) throw new Exception("SKU tidak valid: " . $sku);
            $rowSatuan = $resSatuan->fetch_assoc();
            $id_satuan_dasar = $rowSatuan['ID_Satuan_Dasar'];

            // Konversi jika perlu
            $jumlah_potong_riil = $jumlah_jual; 
            if ($id_satuan_jual != $id_satuan_dasar) {
                $stmtKonversi->bind_param("sii", $sku, $id_satuan_jual, $id_satuan_dasar);
                $stmtKonversi->execute();
                $resKonv = $stmtKonversi->get_result();
                if ($resKonv->num_rows === 0) throw new Exception("Konversi satuan tidak ditemukan: " . $sku);
                $rowKonv = $resKonv->fetch_assoc();
                $jumlah_potong_riil = $jumlah_jual * $rowKonv['Nilai_Konversi'];
            }

            // Insert Detail Penjualan
            $stmtDetail->bind_param("isdid", $id_penjualan, $sku, $jumlah_jual, $id_satuan_jual, $harga_jual);
            if (!$stmtDetail->execute()) throw new Exception("Gagal menyimpan detail penjualan: " . $sku);

            // Potong Stok
            $stmtPotongStok->bind_param("isi", $jumlah_potong_riil, $sku, $jumlah_potong_riil);
            $stmtPotongStok->execute();
            if ($stmtPotongStok->affected_rows === 0) throw new Exception("Stok tidak mencukupi untuk: " . $sku);
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil diproses', 'data' => ['ID_Penjualan' => $id_penjualan]]);

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(400); 
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>