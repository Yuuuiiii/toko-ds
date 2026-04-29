<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$userData = verifyJWT();

if ($method === 'GET') {
    // Menarik riwayat barang masuk beserta nama barang dan suppliernya
    $query = "SELECT bm.*, b.Nama_Barang, s.Nama_Supplier, p.Nama_Lengkap AS Nama_Admin 
              FROM barang_masuk bm
              JOIN barang b ON bm.SKU_Barang = b.SKU_Barang
              LEFT JOIN supplier s ON bm.ID_Supplier = s.ID_Supplier
              JOIN pengguna p ON bm.ID_Pengguna = p.ID_Pengguna
              ORDER BY bm.Tanggal_Masuk DESC LIMIT 100";
              
    $res = $conn->query($query);
    $data = [];
    while($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $data]);
}
elseif ($method === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);
    $barcode = $data['Barcode'] ?? '';
    $qty = intval($data['Qty'] ?? 1);
    $id_supplier = !empty($data['ID_Supplier']) ? $data['ID_Supplier'] : NULL;
    $keterangan = $data['Keterangan'] ?? '';
    $tanggal = !empty($data['Tanggal']) ? $data['Tanggal'] : date('Y-m-d H:i:s');

    // 1. Cek: Barcode Master (Satuan)?
    $stmt = $conn->prepare("SELECT SKU_Barang FROM barang WHERE SKU_Barang = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $resMaster = $stmt->get_result();

    $sku_final = "";
    $jumlah_final = 0;

    if ($resMaster->num_rows > 0) {
        $sku_final = $barcode;
        $jumlah_final = $qty; // 1 Scan = 1 Pcs
    } else {
        // 2. Cek: Barcode Bulk (Kerdus)?
        $stmt = $conn->prepare("SELECT SKU_Master, Isi_Per_Bulk FROM barang_konversi WHERE Barcode_Bulk = ?");
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $resBulk = $stmt->get_result();

        if ($resBulk->num_rows > 0) {
            $row = $resBulk->fetch_assoc();
            $sku_final = $row['SKU_Master'];
            $jumlah_final = $qty * $row['Isi_Per_Bulk']; // 1 Scan Kerdus = (Misal: 40 Pcs)
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Barcode tidak ditemukan di Master maupun Konversi Kerdus!']);
            exit;
        }
    }

    $conn->begin_transaction();
    try {

        $stmt = $conn->prepare("INSERT INTO barang_masuk (Tanggal_Masuk, SKU_Barang, Jumlah_Masuk, ID_Pengguna, Keterangan) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiis", $tanggal, $sku_final, $jumlah_final, $userData['id'], $keterangan);
        $stmt->execute();

        // Tambah Stok Realtime
        $stmt = $conn->prepare("UPDATE stok_barang SET Jumlah_Stok = Jumlah_Stok + ? WHERE SKU_Barang = ?");
        $stmt->bind_param("is", $jumlah_final, $sku_final);
        $stmt->execute();

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => "Berhasil memproses masuk $jumlah_final Pcs ke gudang."]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Gagal memproses data.']);
    }
}
?>