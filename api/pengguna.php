<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../includes/db.php';
require_once '../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$userData = verifyJWT(); // Proteksi API

// Keamanan Tambahan: HANYA ADMIN yang boleh mengakses API ini
if (strtolower($userData['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak! Anda bukan Admin.']);
    exit;
}

// 1. GET: Ambil semua data pengguna (kecuali password)
if ($method === 'GET') {
    $res = $conn->query("SELECT ID_Pengguna, Nama_Lengkap, Username, Peran FROM pengguna ORDER BY ID_Pengguna DESC");
    $data = [];
    while($row = $res->fetch_assoc()) { $data[] = $row; }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// 2. POST: Tambah Pengguna Baru
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nama = $input['Nama_Lengkap'] ?? '';
    $username = $input['Username'] ?? '';
    $password_plain = $input['Password'] ?? '';
    $peran = $input['Peran'] ?? '';
    
    if (empty($nama) || empty($username) || empty($password_plain) || empty($peran)) { 
        echo json_encode(['status' => 'error', 'message' => 'Semua kolom wajib diisi!']); exit; 
    }

    // Hash Password sebelum disimpan!
    $password_hashed = password_hash($password_plain, PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO pengguna (Nama_Lengkap, Username, Password, Peran) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $username, $password_hashed, $peran);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Pengguna berhasil ditambahkan!']);
    } catch (Exception $e) { 
        echo json_encode(['status' => 'error', 'message' => 'Gagal! Username mungkin sudah digunakan.']); 
    }
    exit;
}

// 3. PUT: Edit Data Pengguna
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['ID_Pengguna'] ?? 0;
    $nama = $input['Nama_Lengkap'] ?? '';
    $username = $input['Username'] ?? '';
    $peran = $input['Peran'] ?? '';
    $password_plain = $input['Password'] ?? ''; // Opsional

    if (empty($id) || empty($nama) || empty($username) || empty($peran)) { 
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']); exit; 
    }

    try {
        if (!empty($password_plain)) {
            // Jika password diisi, update passwordnya juga
            $password_hashed = password_hash($password_plain, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE pengguna SET Nama_Lengkap=?, Username=?, Password=?, Peran=? WHERE ID_Pengguna=?");
            $stmt->bind_param("ssssi", $nama, $username, $password_hashed, $peran, $id);
        } else {
            // Jika password kosong, biarkan password lama
            $stmt = $conn->prepare("UPDATE pengguna SET Nama_Lengkap=?, Username=?, Peran=? WHERE ID_Pengguna=?");
            $stmt->bind_param("sssi", $nama, $username, $peran, $id);
        }
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Data pengguna berhasil diperbarui!']);
    } catch (Exception $e) { 
        echo json_encode(['status' => 'error', 'message' => 'Gagal update. Username mungkin bentrok.']); 
    }
    exit;
}

// 4. DELETE: Hapus Pengguna
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['ID_Pengguna'] ?? 0;

    if ($id == $userData['id']) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak bisa menghapus akun Anda sendiri!']); exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM pengguna WHERE ID_Pengguna=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Pengguna berhasil dihapus!']);
    } catch (Exception $e) { 
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pengguna. (Kemungkinan masih ada riwayat transaksi terkait).']); 
    }
    exit;
}
?>