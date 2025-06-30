<?php
require_once '../config.php';

$id = $_POST['id'];
$nama_equipment = $_POST['nama_equipment'];
$location_id = $_POST['location_id'];
$status = $_POST['status'];
$keterangan = $_POST['keterangan'];

$stmt = $conn->prepare("UPDATE equipment SET nama_equipment = ?, location_id = ?, status = ?, keterangan = ? WHERE id = ?");
$stmt->bind_param("ssssi", $nama_equipment, $location_id, $status, $keterangan, $id);

if ($stmt->execute()) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Equipment berhasil diupdate!', 'success');
        }
        setTimeout(() => {
            window.location.href='view_equipment.php';
        }, 1000);
    </script>";
} else {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Gagal update equipment!', 'danger');
        }
        history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>