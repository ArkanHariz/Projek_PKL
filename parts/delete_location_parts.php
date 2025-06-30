<?php
require_once '../config.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM location_parts WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Lokasi parts berhasil dihapus!', 'success');
        }
        setTimeout(() => {
            window.location.href='view_location_parts.php';
        }, 1000);
    </script>";
} else {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Gagal hapus lokasi parts!', 'danger');
        }
        history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>