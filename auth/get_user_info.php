<?php
require_once 'session_check.php';

header('Content-Type: application/json');

if (!checkSession()) {
    echo json_encode([
        'success' => false,
        'message' => 'Not logged in'
    ]);
    exit;
}

$userInfo = getUserInfo();
echo json_encode([
    'success' => true,
    'user' => $userInfo
]);
?>