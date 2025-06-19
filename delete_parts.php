<?php
require_once 'config.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM parts WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil dihapus'); window.location.href='view_parts.php'</script>";
} else {
    echo "<script>alert('Gagal Hapus'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>