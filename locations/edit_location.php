<?php
require_once '../config.php';

$id = $_POST['id'];
$nama_location = $_POST['nama_location'];
$keterangan = $_POST['keterangan'];

$stmt = $conn->prepare("UPDATE locations SET nama_location = ?, keterangan = ? WHERE id = ?");
$stmt->bind_param("ssi", $nama_location, $keterangan, $id);

if ($stmt->execute()) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Lokasi berhasil diupdate!', 'success');
        }
        setTimeout(() => {
            window.location.href='view_locations.php';
        }, 1000);
    </script>";
} else {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Gagal update lokasi!', 'danger');
        }
        history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>