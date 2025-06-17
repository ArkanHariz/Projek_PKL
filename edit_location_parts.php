<?php
require_once 'config.php';

$id = $_POST['id'];
$nama_location_part = $_POST['nama_location_part'];
$keterangan = $_POST['keterangan'];

$stmt = $conn->prepare("UPDATE location_parts SET nama_location_part = ?, keterangan = ? WHERE id = ?");
$stmt->bind_param("ssi", $nama_location_part, $keterangan, $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil diupdate'); window.location.href='view_location_parts.php';</script>";
} else {
    echo "<script>alert('Gagal update'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>