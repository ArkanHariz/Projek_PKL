<?php
require_once '../config.php';

$username = htmlspecialchars($_POST['username'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (!empty($username) && !empty($email) && !empty($password) && !empty($role)) {
    // Cek apakah username atau email sudah digunakan
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Username atau email sudah terdaftar.'); window.history.back();</script>";
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Simpan user baru
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Data berhasil disimpan!'); window.location.href='../main.html';</script>";
} else {
    echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>";
}

$conn->close();
?>