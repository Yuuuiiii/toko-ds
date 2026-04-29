<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
verifyJWT(); // Proteksi API

// 1. GET: Ambil semua member ATAU cari by HP
if ($method === 'GET') {
    if (isset($_GET['hp'])) {
        $hp = $_GET['hp'];
        $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE No_HP = ? LIMIT 1");
        $stmt->bind_param("s", $hp);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) echo json_encode(['status' => 'success', 'data' => $res->fetch_assoc()]);
        else echo json_encode(['status' => 'not_found', 'message' => 'Member tidak ditemukan.']);
    } else {
        // Ambil semua data untuk tabel di halaman Owner
        $res = $conn->query("SELECT * FROM pelanggan ORDER BY Tanggal_Daftar DESC");
        $data = [];
        while($row = $res->fetch_assoc()) { $data[] = $row; }
        echo json_encode(['status' => 'success', 'data' => $data]);
    }
    exit;
}

// 2. POST: Daftar Member Baru
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nama = $input['Nama_Pelanggan'] ?? '';
    $hp = $input['No_HP'] ?? '';
    
    if (empty($nama) || empty($hp)) { echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']); exit; }

    try {
        $stmt = $conn->prepare("INSERT INTO pelanggan (Nama_Pelanggan, No_HP) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $hp);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'data' => ['ID_Pelanggan' => $conn->insert_id, 'Nama_Pelanggan' => $nama, 'No_HP' => $hp]]);
    } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftar. No HP mungkin sudah digunakan.']); }
    exit;
}

// 3. PUT: Edit Data Member
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['ID_Pelanggan'] ?? 0;
    $nama = $input['Nama_Pelanggan'] ?? '';
    $hp = $input['No_HP'] ?? '';

    if (empty($id) || empty($nama) || empty($hp)) { echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']); exit; }

    try {
        $stmt = $conn->prepare("UPDATE pelanggan SET Nama_Pelanggan=?, No_HP=? WHERE ID_Pelanggan=?");
        $stmt->bind_param("ssi", $nama, $hp, $id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Data member berhasil diperbarui!']);
    } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => 'Gagal update. No HP mungkin dipakai orang lain.']); }
    exit;
}

// 4. DELETE: Hapus Member
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['ID_Pelanggan'] ?? 0;

    if (empty($id)) { echo json_encode(['status' => 'error', 'message' => 'ID kosong!']); exit; }

    try {
        $stmt = $conn->prepare("DELETE FROM pelanggan WHERE ID_Pelanggan=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Member berhasil dihapus!']);
    } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus member.']); }
    exit;
}
?>