<?php
require_once '../config.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('User berhasil dihapus!', 'success');
        }
        setTimeout(() => {
            window.location.href='view_users.php';
        }, 1000);
    </script>";
} else {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Gagal hapus user!', 'danger');
        }
        history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>