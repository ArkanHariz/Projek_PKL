<?php
require_once '../config.php';

$id = $_POST['id'];
$username = htmlspecialchars($_POST['username']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$role = $_POST['role'];

if (empty($id) || empty($username) || empty($email) || empty($role)) {
    echo "<script>alert('Semua field wajib diisi.'); history.back();</script>";
    exit;
}

$stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
$stmt->bind_param("sssi", $username, $email, $role, $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil diupdate'); window.location.href='view_users.php';</script>";
} else {
    echo "<script>alert('Gagal update: " . $stmt->error . "'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>