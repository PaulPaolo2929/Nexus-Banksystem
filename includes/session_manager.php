<?php
// Set the timeout duration (15 minutes in seconds)
$timeoutDuration = 900;  // 15 minutes

// Set the logout redirect URL
$logoutRedirectUrl = '../logout.php';

// Function to check session status
function checkSessionStatus() {
    global $timeoutDuration, $logoutRedirectUrl;
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }

    // Initialize last_activity if not set
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }

    // Check if session has expired
    if ((time() - $_SESSION['last_activity']) > $timeoutDuration) {
        // If session expired, log out user
        session_unset();
        session_destroy();
        header("Location: $logoutRedirectUrl?timeout=1");
        exit();
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
}

// Function to get remaining session time in seconds
function getRemainingSessionTime() {
    global $timeoutDuration;
    
    if (!isset($_SESSION['last_activity'])) {
        return $timeoutDuration;
    }
    
    $elapsed = time() - $_SESSION['last_activity'];
    return max(0, $timeoutDuration - $elapsed);
}
?> 