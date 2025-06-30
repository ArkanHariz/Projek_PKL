<?php
require_once '../config.php';

header('Content-Type: application/json');

$nama_equipment = $_POST['nama_equipment'] ?? '';
$location_id = $_POST['location_id'] ?? '';
$status = $_POST['status'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_equipment) && !empty($location_id) && !empty($status)) {
    // Check if equipment already exists
    $checkStmt = $conn->prepare("SELECT id FROM equipment WHERE nama_equipment = ? AND location_id = ?");
    $checkStmt->bind_param("si", $nama_equipment, $location_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Equipment dengan nama tersebut sudah ada di lokasi yang sama.'
        ]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();
    
    $stmt = $conn->prepare("INSERT INTO equipment (nama_equipment, location_id, status, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_equipment, $location_id, $status, $keterangan);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Equipment berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan equipment: ' . $stmt->error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nama equipment, lokasi, dan status wajib diisi.'
    ]);
}

$conn->close();
?>