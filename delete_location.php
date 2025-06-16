<?php
require_once 'config.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM locations WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil dihapus'); window.location.href='view_locations.php';</script>";
} else {
    echo "<script>alert('Gagal hapus'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>