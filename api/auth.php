<?php
// 1. CORS HEADERS - Wajib untuk integrasi dengan Frontend React
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

// Handle Preflight Request dari browser (CORS)
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Username dan password wajib diisi.']);
        exit;
    }

    try {
        // Ambil data user
        $stmt = $conn->prepare("SELECT ID_Pengguna, Username, Password, Nama_Lengkap, Peran FROM pengguna WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Username atau password salah.']);
            exit;
        }

        $user = $result->fetch_assoc();
        $db_password = $user['Password'];

        // 2. SELF-HEALING HASH LOGIC (Transisi MD5 ke Bcrypt)
        $is_valid = false;
        $needs_rehash = false;

        // Cek apakah formatnya masih MD5 (panjang 32 karakter hexadecimal)
        if (strlen($db_password) === 32 && ctype_xdigit($db_password)) {
            if (md5($password) === $db_password) {
                $is_valid = true;
                $needs_rehash = true; // Tandai untuk diperbarui
            }
        } else {
            // Gunakan standar modern PHP (Bcrypt)
            if (password_verify($password, $db_password)) {
                $is_valid = true;
            }
        }

        if (!$is_valid) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Username atau password salah.']);
            exit;
        }

        // Auto-upgrade password di DB jika masih MD5
        if ($needs_rehash) {
            $new_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt_update = $conn->prepare("UPDATE pengguna SET Password = ? WHERE ID_Pengguna = ?");
            $stmt_update->bind_param("si", $new_hash, $user['ID_Pengguna']);
            $stmt_update->execute();
        }

        // 3. GENERATE NATIVE JWT (JSON Web Token)
        $secret_key = "RAHASIA_SISTEM_TOKO_DS"; // Catatan: Di production, simpan ini di .env
        
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'id' => $user['ID_Pengguna'],
            'username' => $user['Username'],
            'role' => $user['Peran'],
            'exp' => time() + (86400 * 7) // Token valid 7 hari
        ]);

        // Encode Base64URL
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        // Buat Signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        // Gabungkan jadi JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        echo json_encode([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'token' => $jwt,
            'user' => [
                'id' => $user['ID_Pengguna'],
                'nama' => $user['Nama_Lengkap'],
                'role' => $user['Peran']
            ]
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan sistem internal.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
exit;
?>