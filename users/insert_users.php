<?php
require_once '../config.php';
require_once '../auth/session_check.php';

// Check if user is logged in and is Admin
requireLogin();
$userRole = getUserRole();
if ($userRole !== 'Admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Only administrators can create users.'
    ]);
    exit;
}

header('Content-Type: application/json');

$username = htmlspecialchars($_POST['username'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if (!empty($username) && !empty($email) && !empty($password) && !empty($role)) {
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Format email tidak valid.'
        ]);
        exit;
    }
    
    // Cek apakah username atau email sudah digunakan
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Username atau email sudah terdaftar.'
        ]);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Simpan user baru
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'User berhasil ditambahkan!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal menambahkan user: ' . $stmt->error
        ]);
    }
    $stmt->close();

} else {
    echo json_encode([
        'success' => false,
        'message' => 'Semua field wajib diisi.'
    ]);
}

$conn->close();
?>