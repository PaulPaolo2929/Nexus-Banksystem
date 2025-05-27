<?php
session_start();
require_once '../includes/session_manager.php';

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Not logged in',
            'redirect' => '../login.php'
        ]);
        exit;
    }

    // Check if session has expired
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutDuration) {
        session_unset();
        session_destroy();
        echo json_encode([
            'success' => false,
            'message' => 'Session expired',
            'redirect' => '../logout.php?timeout=1'
        ]);
        exit;
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Get remaining session time
    $remainingTime = getRemainingSessionTime();
    
    echo json_encode([
        'success' => true,
        'remainingTime' => $remainingTime,
        'message' => 'Session refreshed successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error refreshing session: ' . $e->getMessage()
    ]);
}
?> 