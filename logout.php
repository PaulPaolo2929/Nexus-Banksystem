<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Check if timeout parameter is set
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    // Output HTML + JS for alert before redirect
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Session Timeout</title>
        <script>
            alert("Your session has timed out due to inactivity. You will be redirected to the login page.");
            window.location.href = "login.php";
        </script>
    </head>
    <body></body>
    </html>';
    exit();
} else {
    // Normal logout, no alert
    header("Location: login.php");
    exit();
}
