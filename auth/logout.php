<?php
session_start();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
    
    // Clear token from database
    if (isset($_SESSION['user_id'])) {
        require_once '../config.php';
        $stmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

// Destroy session
session_destroy();

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully'
]);
?>