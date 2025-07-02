<?php
require_once '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Log all incoming data for debugging
error_log("Forgot password request received");
error_log("POST data: " . print_r($_POST, true));

$identifier = trim($_POST['identifier'] ?? '');
$action = $_POST['action'] ?? 'search';
$user_id = $_POST['user_id'] ?? '';

// Add debug logging
error_log("Forgot password request - Action: $action, Identifier: $identifier");

if ($action === 'search') {
    // Search for user account
    if (empty($identifier)) {
        error_log("Empty identifier provided");
        echo json_encode([
            'success' => false,
            'message' => 'Username or email is required.'
        ]);
        exit;
    }

    try {
        error_log("Searching for user with identifier: $identifier");
        
        // Search by username or email (simplified - no password display)
        $stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE username = ? OR email = ?");
        
        if (!$stmt) {
            error_log("Prepare statement failed: " . $conn->error);
            echo json_encode([
                'success' => false,
                'message' => 'Database prepare error: ' . $conn->error
            ]);
            exit;
        }
        
        $stmt->bind_param("ss", $identifier, $identifier);
        
        if (!$stmt->execute()) {
            error_log("Execute statement failed: " . $stmt->error);
            echo json_encode([
                'success' => false,
                'message' => 'Database execute error: ' . $stmt->error
            ]);
            exit;
        }
        
        $result = $stmt->get_result();
        error_log("Query executed, rows found: " . $result->num_rows);
        
        if ($result->num_rows === 0) {
            error_log("No user found with identifier: $identifier");
            echo json_encode([
                'success' => false,
                'message' => 'No account found with that username or email.'
            ]);
            exit;
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        error_log("User found: " . print_r($user, true));
        
        // Return user info (without password - will generate new one)
        echo json_encode([
            'success' => true,
            'message' => 'Account found successfully.',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
        
    } catch (Exception $e) {
        error_log("Forgot password error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while searching for your account: ' . $e->getMessage()
        ]);
    }
    
} elseif ($action === 'reset_password') {
    // Reset password with user input
    if (empty($user_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID is required.'
        ]);
        exit;
    }

    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate passwords
    if (empty($new_password) || empty($confirm_password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Both password fields are required.'
        ]);
        exit;
    }

    if ($new_password !== $confirm_password) {
        echo json_encode([
            'success' => false,
            'message' => 'Passwords do not match.'
        ]);
        exit;
    }

    if (strlen($new_password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Password must be at least 6 characters long.'
        ]);
        exit;
    }

    try {
        // Get user info
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'User not found.'
            ]);
            exit;
        }
        
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update user password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Password has been reset successfully.',
                'username' => $user['username']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to reset password.'
            ]);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Reset password error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred while resetting password.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action specified.'
    ]);
}

$conn->close();
?>