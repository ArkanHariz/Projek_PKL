<?php
require_once 'config.php';

$id = $_POST['id'];
$nama_location = $_POST['nama_location'];
$keterangan = $_POST['keterangan'];

$stmt = $conn->prepare("UPDATE locations SET nama_location = ?, keterangan = ? WHERE id = ?");
$stmt->bind_param("ssi", $nama_location, $keterangan, $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil diupdate'); window.location.href='view_locations.php';</script>";
} else {
    echo "<script>alert('Gagal update'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>