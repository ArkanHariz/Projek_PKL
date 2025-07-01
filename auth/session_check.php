<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkSession() {
    // Check if user is logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return false;
    }
    
    // Check session timeout (optional - 2 hours)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 7200) {
        session_destroy();
        return false;
    }
    
    return true;
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function getUserInfo() {
    if (!checkSession()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ];
}

function requireLogin() {
    if (!checkSession()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // AJAX request
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'redirect' => 'login.html'
            ]);
        } else {
            // Regular request
            header('Location: ../login.html');
        }
        exit;
    }
}

function hasPermission($permission) {
    $role = getUserRole();
    
    switch ($role) {
        case 'Admin':
            return true; // Admin has all permissions
        case 'Viewer':
            return $permission === 'view'; // Viewer can only view
        case 'Dispatch':
            return in_array($permission, ['view', 'create']); // Dispatch can view and create
        case 'Technician':
            return in_array($permission, ['view', 'create', 'update']); // Technician can view, create, and update
        default:
            return false;
    }
}
?>