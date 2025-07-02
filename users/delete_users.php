<?php
require_once '../config.php';
require_once '../auth/session_check.php';

// Check if user is logged in and is Admin
requireLogin();
$userRole = getUserRole();
if ($userRole !== 'Admin') {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Access denied. Only administrators can delete users.', 'danger');
        }
        history.back();
    </script>";
    exit;
}

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