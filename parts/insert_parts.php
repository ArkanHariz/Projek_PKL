<?php
require_once '../config.php';

header('Content-Type: application/json');

$nama_parts = $_POST['partName'] ?? '';
$location_part_id = $_POST['location_part_id'] ?? '';
$equipmentLocation = $_POST['equipmentLocation'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_parts) && !empty($location_part_id) && !empty($equipmentLocation)) {
    // Check if part already exists
    $checkStmt = $conn->prepare("SELECT id FROM parts WHERE nama_part = ? AND location_part_id = ? AND equipment_id = ?");
    $checkStmt->bind_param("sii", $nama_parts, $location_part_id, $equipmentLocation);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Parts dengan nama tersebut sudah ada di lokasi dan equipment yang sama.'
        ]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();
    
    $stmt = $conn->prepare("INSERT INTO parts (nama_part, location_part_id, equipment_id, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siis", $nama_parts, $location_part_id, $equipmentLocation, $keterangan);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Parts berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan parts: ' . $stmt->error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nama parts, lokasi parts, dan equipment wajib diisi.'
    ]);
}

$conn->close();
?>