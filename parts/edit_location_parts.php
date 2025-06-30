<?php
require_once '../config.php';

$id = $_POST['id'];
$nama_location_part = $_POST['nama_location_part'];
$keterangan = $_POST['keterangan'];

$stmt = $conn->prepare("UPDATE location_parts SET nama_location_part = ?, keterangan = ? WHERE id = ?");
$stmt->bind_param("ssi", $nama_location_part, $keterangan, $id);

if ($stmt->execute()) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Lokasi parts berhasil diupdate!', 'success');
        }
        setTimeout(() => {
            window.location.href='view_location_parts.php';
        }, 1000);
    </script>";
} else {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Gagal update lokasi parts!', 'danger');
        }
        history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>