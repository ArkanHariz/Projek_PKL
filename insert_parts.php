<?php
require_once 'config.php';

$nama_parts = $_POST['partName'] ?? '';
$location_part_id = $_POST['location_part_id'] ?? '';
$equipmentLocation = $_POST['equipmentLocation'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_parts)) {
    $stmt = $conn->prepare("INSERT INTO parts (nama_part, location_part_id, equipment_id, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_parts, $location_part_id, $equipmentLocation, $keterangan);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Data berhasil disimpan!'); window.location.href='main.html';</script>";
} else {
    echo "<script>alert('Nama parts wajib diisi.'); window.history.back();</script>";
}

$conn->close();
?>