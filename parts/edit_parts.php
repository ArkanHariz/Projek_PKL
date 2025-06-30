<?php
require_once '../config.php';

$id = $_POST['id'];
$nama_parts = $_POST['partName'];
$location_part_id = $_POST['location_part_id'];
$equipmentLocation = $_POST['equipmentLocation'];
$keterangan = $_POST['keterangan'];

$stmt = $conn->prepare("UPDATE parts SET nama_part = ?, location_part_id = ?, equipment_id = ?, keterangan = ? WHERE id = ?");
$stmt->bind_param("ssssi", $nama_parts, $location_part_id, $equipmentLocation, $keterangan, $id);

if ($stmt->execute()) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Parts berhasil diupdate!', 'success');
        }
        setTimeout(() => {
            window.location.href='view_parts.php';
        }, 1000);
    </script>";
} else {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Gagal update parts!', 'danger');
        }
        history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>