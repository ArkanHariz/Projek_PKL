<?php
require_once 'config.php';

$result = $conn->query("SELECT id, nama_location_part FROM location_parts");

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

echo json_encode($locations);
?>