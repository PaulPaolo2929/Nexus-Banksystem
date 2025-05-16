<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    // Update last activity time
    $_SESSION['last_activity'] = time();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?> 