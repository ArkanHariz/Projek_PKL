<?php
require_once '../config.php';

$result = $conn->query("SELECT id, nama_location FROM locations");

$locations = [];
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}

echo json_encode($locations);
?>
