<?php
require_once '../config.php';

header('Content-Type: application/json');

$nama_location = $_POST['nama_location'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_location)) {
    // Check if location already exists
    $checkStmt = $conn->prepare("SELECT id FROM locations WHERE nama_location = ?");
    $checkStmt->bind_param("s", $nama_location);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Lokasi dengan nama tersebut sudah ada.'
        ]);
        $checkStmt->close();
        $conn->close();
        exit;
    }
    $checkStmt->close();
    
    $stmt = $conn->prepare("INSERT INTO locations (nama_location, keterangan) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_location, $keterangan);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Lokasi berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan lokasi: ' . $stmt->error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Nama lokasi wajib diisi.'
    ]);
}

$conn->close();
?>