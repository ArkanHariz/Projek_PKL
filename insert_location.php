<?php
require_once 'config.php';

$nama_location = $_POST['nama_location'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';

if (!empty($nama_location)) {
    $stmt = $conn->prepare("INSERT INTO locations (nama_location, keterangan) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama_location, $keterangan);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Data berhasil disimpan!'); window.location.href='main.html';</script>";
} else {
    echo "<script>alert('Nama lokasi wajib diisi.'); window.history.back();</script>";
}

$conn->close();
?>