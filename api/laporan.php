<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../includes/db.php'; 

$type = $_GET['type'] ?? '';

try {
    $data = [];
    
    // 1. Tarik Riwayat Transaksi (SUDAH DIUPDATE DENGAN RUMUS KEUNTUNGAN)
    if ($type === 'riwayat') {
        $query = "SELECT 
                    p.ID_Penjualan,
                    p.Tanggal_Penjualan AS Waktu_Transaksi,
                    pg.Nama_Lengkap AS Kasir,
                    p.Metode_Pembayaran,
                    p.Total_Harga AS Total_Penjualan,
                    (p.Total_Harga - COALESCE(modal.Total_Modal, 0)) AS Keuntungan
                  FROM penjualan p
                  JOIN pengguna pg ON p.ID_Kasir = pg.ID_Pengguna
                  LEFT JOIN (
                      SELECT dp.ID_Penjualan, SUM(dp.Jumlah_Jual * COALESCE(b.Harga_Beli, 0)) as Total_Modal
                      FROM detail_penjualan dp
                      LEFT JOIN barang b ON dp.SKU_Barang = b.SKU_Barang
                      GROUP BY dp.ID_Penjualan
                  ) modal ON p.ID_Penjualan = modal.ID_Penjualan
                  ORDER BY p.Tanggal_Penjualan DESC";
                  
        $result = $conn->query($query);
        if ($result) { foreach($result as $row) { $data[] = $row; } }
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    }
    
    // 2. Tarik Detail Barang per Transaksi
    elseif ($type === 'detail') {
        $id_penjualan = $_GET['id'] ?? 0;
        $query = "SELECT 
                    dp.SKU_Barang,
                    b.Nama_Barang,
                    dp.Harga_Saat_Jual,
                    dp.Jumlah_Jual,
                    dp.Subtotal
                  FROM detail_penjualan dp
                  JOIN barang b ON dp.SKU_Barang = b.SKU_Barang
                  WHERE dp.ID_Penjualan = " . intval($id_penjualan);
                  
        $result = $conn->query($query);
        if ($result) { foreach($result as $row) { $data[] = $row; } }
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    } 
    
    // 3. Tarik Rekap Harian (AMAN! TIDAK DIHAPUS)
    elseif ($type === 'harian') {
        $query = "SELECT * FROM v_penjualan_harian 
                  WHERE Tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
                  ORDER BY Tanggal DESC";
                  
        $result = $conn->query($query);
        $db_data = [];
        if ($result) { foreach($result as $row) { $db_data[] = $row; } }

        $calendar_data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $found = false;
            foreach ($db_data as $row) {
                if ($row['Tanggal'] === $date) {
                    $calendar_data[] = $row;
                    $found = true;
                }
            }
            if (!$found) {
                $calendar_data[] = [
                    'Tanggal' => $date,
                    'Jumlah_Transaksi' => 0,
                    'Total_Penjualan' => 0,
                    'Rata_Rata_Transaksi' => 0,
                    'Transaksi_Terbesar' => 0,
                    'Transaksi_Terkecil' => 0,
                    'Kasir' => 'Tidak Ada Transaksi',
                    'Metode_Pembayaran' => '-'
                ];
            }
        }
        
        echo json_encode(['status' => 'success', 'data' => $calendar_data]);
    }
    
    // 4. Tarik Summary 4 Kotak (AMAN! TIDAK DIHAPUS)
    elseif ($type === 'summary') {
        $query = "SELECT * FROM v_dashboard_summary LIMIT 1";
        $result = $conn->query($query);
        $summary_data = null;
        
        if ($result) { 
            foreach($result as $row) { $summary_data = $row; } 
        }
        
        if ($summary_data) {
            $summary_data['Penjualan_Hari_Ini'] = $summary_data['Penjualan_Hari_Ini'] ?? 0;
            $summary_data['Transaksi_Hari_Ini'] = $summary_data['Transaksi_Hari_Ini'] ?? 0;
            $summary_data['Penjualan_Bulan_Ini'] = $summary_data['Penjualan_Bulan_Ini'] ?? 0;
            $summary_data['Nilai_Inventori_Total'] = $summary_data['Nilai_Inventori_Total'] ?? 0;
        }
        
        echo json_encode(['status' => 'success', 'data' => $summary_data]);
    }
    
    else {
        echo json_encode(['status' => 'error', 'message' => 'Tipe request tidak valid']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>