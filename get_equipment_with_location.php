<?php
require_once 'config.php';

$sql = "SELECT equipment.id, equipment.nama_equipment, locations.nama_location
        FROM equipment
        INNER JOIN locations ON equipment.location_id = locations.id";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'label' => $row['nama_equipment'] . ' - ' . $row['nama_location']
    ];
}

echo json_encode($data);
?>