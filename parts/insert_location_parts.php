<?php
require_once '../config.php';

header('Content-Type: application/json');

$nama_location_part = $_POST['nama_location_part'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_location_part)) {
    // Check if location part already exists
    $checkStmt = $conn->prepare("SELECT id FROM location_parts WHERE nama_location_part = ?");
    $checkStmt->bind_param("s", $nama_location_part);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Lokasi parts dengan nama tersebut sudah ada.'
        ]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();
    
    $stmt = $conn->prepare("INSERT INTO location_parts (nama_location_part, keterangan) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_location_part, $keterangan);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Lokasi parts berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan lokasi parts: ' . $stmt->error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nama lokasi parts wajib diisi.'
    ]);
}

$conn->close();
?>