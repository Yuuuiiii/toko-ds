<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT ID_Satuan, Nama_Satuan FROM satuan ORDER BY Nama_Satuan ASC";
    $result = $conn->query($query);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}
?>