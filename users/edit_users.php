<?php
require_once '../config.php';
require_once '../auth/session_check.php';

// Check if user is logged in and is Admin
requireLogin();
$userRole = getUserRole();
if ($userRole !== 'Admin') {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Access denied. Only administrators can edit users.', 'danger');
        }
        history.back();
    </script>";
    exit;
}

$id = $_POST['id'];
$username = htmlspecialchars($_POST['username']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$role = $_POST['role'];

if (empty($id) || empty($username) || empty($email) || empty($role)) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Semua field wajib diisi!', 'danger');
        }
        history.back();
    </script>";
    exit;
}

$stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
$stmt->bind_param("sssi", $username, $email, $role, $id);

if ($stmt->execute()) {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('User berhasil diupdate!', 'success');
        }
        setTimeout(() => {
            window.location.href='view_users.php';
        }, 1000);
    </script>";
} else {
    echo "<script>
        if (window.parent && window.parent.showToast) {
            window.parent.showToast('Gagal update user: " . $stmt->error . "', 'danger');
        }
        history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>