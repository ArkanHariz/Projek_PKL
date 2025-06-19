<?php
require_once '../config.php';

header('Content-Type: application/json');

$sql = "SELECT 
            equipment.id, 
            equipment.nama_equipment, 
            locations.nama_location
        FROM equipment
        INNER JOIN locations ON equipment.location_id = locations.id";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Query error: ' . $conn->error
    ]);
    exit;
}

if ($result->num_rows === 0) {
    echo json_encode([
        'error' => false,
        'message' => 'No data found',
        'data' => []
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'nama_equipment' => $row['nama_equipment'],
        'nama_location' => $row['nama_location']
    ];
}

echo json_encode($data);
?>