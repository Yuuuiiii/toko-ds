<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    // BACA DARI VIEW AJAIB BUATANMU BIAR STATUS & STOKNYA MUNCUL
    $res = $conn->query("SELECT * FROM v_laporan_stok ORDER BY Nama_Barang ASC");
    
    if (!$res) {
        echo json_encode(['status' => 'error', 'message' => 'Error SQL: ' . $conn->error]);
        exit;
    }

    $barang = [];
    while($row = $res->fetch_assoc()) { 
        $barang[] = $row; 
    }
    echo json_encode(['status' => 'success', 'data' => $barang]);
}
elseif ($method === 'POST') {
    $sku = $data['SKU_Barang'] ?? '';
    $nama = $data['Nama_Barang'] ?? '';
    $kategori = !empty($data['ID_Kategori']) ? $data['ID_Kategori'] : NULL;
    $satuan = !empty($data['ID_Satuan_Dasar']) ? $data['ID_Satuan_Dasar'] : NULL;
    $harga = $data['Harga_Jual'] ?? 0;
    
    // Karena form-mu ada input Stok Awal, kita tangkap nilainya
    $stok_awal = isset($data['Stok_Tersedia']) ? intval($data['Stok_Tersedia']) : 0;

    $stmt = $conn->prepare("INSERT INTO barang (SKU_Barang, Nama_Barang, ID_Kategori, ID_Satuan_Dasar, Harga_Jual) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiid", $sku, $nama, $kategori, $satuan, $harga);

    if ($stmt->execute()) {
        // Trigger trg_auto_create_stok di databasemu otomatis bikin stok = 0.
        // Jadi kita tinggal UPDATE jumlahnya jika ada input stok awal.
        if ($stok_awal > 0) {
            $conn->query("UPDATE stok_barang SET Jumlah_Stok = $stok_awal WHERE SKU_Barang = '$sku'");
        }
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambah barang! Pastikan SKU belum pernah dipakai.']);
    }
}
elseif ($method === 'PUT') {
    $sku = $data['SKU_Barang'] ?? '';
    $nama = $data['Nama_Barang'] ?? '';
    $kategori = !empty($data['ID_Kategori']) ? $data['ID_Kategori'] : NULL;
    $satuan = !empty($data['ID_Satuan_Dasar']) ? $data['ID_Satuan_Dasar'] : NULL;
    $harga = $data['Harga_Jual'] ?? 0;
    $stok_update = isset($data['Stok_Tersedia']) ? intval($data['Stok_Tersedia']) : NULL;

    $stmt = $conn->prepare("UPDATE barang SET Nama_Barang=?, ID_Kategori=?, ID_Satuan_Dasar=?, Harga_Jual=? WHERE SKU_Barang=?");
    $stmt->bind_param("siids", $nama, $kategori, $satuan, $harga, $sku);

    if ($stmt->execute()) {
        // Update stok juga kalau di-edit
        if ($stok_update !== NULL) {
            $conn->query("UPDATE stok_barang SET Jumlah_Stok = $stok_update WHERE SKU_Barang = '$sku'");
        }
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate barang!']);
    }
}
elseif ($method === 'DELETE') {
    $sku = $data['SKU_Barang'] ?? '';
    $stmt = $conn->prepare("DELETE FROM barang WHERE SKU_Barang=?");
    $stmt->bind_param("s", $sku);
    
    try {
        $stmt->execute();
        echo json_encode(['status' => 'success']);
    } catch (mysqli_sql_exception $e) {
        // Error karena trigger prevent_delete_barang
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus! Barang ini tidak bisa dihapus karena sudah memiliki riwayat transaksi/penjualan.']);
    }
}
?>