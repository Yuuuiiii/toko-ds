<?php
// includes/auth.php

function verifyJWT() {
    // Ambil header otorisasi (Kompatibel dengan Nginx/Apache)
    $headers = apache_request_headers();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '');

    // Cek apakah format Bearer token ada
    if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Token otorisasi tidak ditemukan.']);
        exit; // Hentikan eksekusi script!
    }

    $jwt = $matches[1];
    $tokenParts = explode('.', $jwt);

    if (count($tokenParts) !== 3) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Format token tidak valid.']);
        exit;
    }

    $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
    $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
    $signature_provided = $tokenParts[2];

    // Secret key (HARUS SAMA dengan yang ada di api/auth.php)
    $secret_key = "RAHASIA_SISTEM_TOKO_DS";

    // Re-create Signature untuk verifikasi
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    $signature_expected = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
    $base64UrlSignatureExpected = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature_expected));

    // Validasi Signature
    if (!hash_equals($base64UrlSignatureExpected, $signature_provided)) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Token signature tidak valid.']);
        exit;
    }

    $payload_data = json_decode($payload, true);

    // Validasi Expiration Time
    if (isset($payload_data['exp']) && $payload_data['exp'] < time()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Sesi telah kadaluarsa. Silakan login kembali.']);
        exit;
    }

    // Jika valid, kembalikan data user agar bisa dipakai oleh API (misal untuk cek Role)
    return $payload_data;
}
?>