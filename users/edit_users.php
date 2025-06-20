<?php
require_once '../config.php';

$id = $_POST['id'];
$username = htmlspecialchars($_POST['username']);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$role = $_POST['role'];

// Validasi input
if (empty($id) || empty($username) || empty($email) || empty($password) || empty($role)) {
    echo "<script>alert('Semua field wajib diisi.'); history.back();</script>";
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Update data
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE id = ?");
$stmt->bind_param("ssssi", $username, $email, $hashedPassword, $role, $id);

if ($stmt->execute()) {
    echo "<script>alert('Data berhasil diupdate'); window.location.href='view_users.php';</script>";
} else {
    echo "<script>alert('Gagal update: " . $stmt->error . "'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>