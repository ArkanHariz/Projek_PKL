<?php
require_once 'config.php';

$nama_location_part = $_POST['nama_location_part'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_location_part)) {
    $stmt = $conn->prepare("INSERT INTO location_parts (nama_location_part, keterangan) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_location_part, $keterangan);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Data berhasil disimpan!'); window.location.href='view_location_parts.php';</script>";
} else {
    echo "<script>alert('Nama lokasi parts wajib diisi.'); window.history.back();</script>";
}

$conn->close();
?>