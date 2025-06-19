<?php
require_once '../config.php';

$nama_equipment = $_POST['nama_equipment'] ?? '';
$location_id = $_POST['location_id'] ?? '';
$status = $_POST['status'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_equipment)) {
    $stmt = $conn->prepare("INSERT INTO equipment (nama_equipment, location_id, status, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_equipment, $location_id, $status, $keterangan);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Data berhasil disimpan!'); window.location.href='../main.html';</script>";
} else {
    echo "<script>alert('Nama equipment wajib diisi.'); window.history.back();</script>";
}

$conn->close();
?>