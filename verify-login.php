<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/notification.php';

// Set timezone to UTC for consistency
date_default_timezone_set('UTC');

// Get IP address and user agent
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$token = $_GET['token'] ?? '';
$action = $_GET['action'] ?? '';

if (empty($token) || empty($action)) {
    header("Location: login.php?error=missing_parameters");
    exit();
}

try {
    // Verify token from database
    $stmt = $pdo->prepare("SELECT v.*, u.email, u.is_admin, v.ip_address, v.user_agent 
                          FROM login_verifications v 
                          JOIN users u ON v.user_id = u.user_id 
                          WHERE v.token = ? AND v.verified = 0 
                          AND v.expires_at > UTC_TIMESTAMP()");
    $stmt->execute([$token]);
    $verification = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$verification) {
        error_log("Invalid verification attempt for token: $token");
        header("Location: login.php?error=invalid_verification");
        exit();
    }

    if ($action === 'verify') {
        $pdo->beginTransaction();
        
        // Mark verification as complete (1 = verified)
        $stmt = $pdo->prepare("UPDATE login_verifications SET verified = 1 WHERE id = ?");
        if (!$stmt->execute([$verification['id']])) {
            error_log("Failed to update verification status for ID: " . $verification['id']);
            throw new Exception("Failed to update verification status");
        }
        
        error_log("Successfully verified login for user ID: " . $verification['user_id']);
        
        $pdo->commit();

        // Send confirmation email
        $subject = "Login Confirmed - Nexus E-Banking";
        $body = "Hello,<br><br>"
              . "You have successfully verified your login to Nexus E-Banking.<br>"
              . "Login details:<br>"
              . "IP Address: " . htmlspecialchars($verification['ip_address'] ?? 'Unknown') . "<br>"
              . "Browser: " . htmlspecialchars($verification['user_agent'] ?? 'Unknown') . "<br><br>"
              . "If this wasn't you, please contact support immediately.<br><br>"
              . "Thank you,<br>Nexus Bank";
        sendNotification($verification['email'], $subject, $body);

        // Show success message
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login Verified - Nexus E-Banking</title>
            <link rel="stylesheet" href="./assets/css/main.css">
            <style>
                .success-container {
                    max-width: 500px;
                    margin: 50px auto;
                    padding: 20px;
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .logo { max-width: 200px; margin-bottom: 30px; }
                .success-icon {
                    color: #4CAF50;
                    font-size: 48px;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="success-container">
                <img src="./assets/images/Logo.png" alt="Nexus Logo" class="logo">
                <h2>Login Verified</h2>
                <div class="success-icon">✓</div>
                <p>Your login has been verified successfully.</p>
                <p>You can close this window and return to the login page.</p>
            </div>
        </body>
        </html>
        <?php
        exit();
    } else {
        $pdo->beginTransaction();
        
        // Mark verification as denied (2 = denied)
        $stmt = $pdo->prepare("UPDATE login_verifications SET verified = 2 WHERE id = ?");
        if (!$stmt->execute([$verification['id']])) {
            error_log("Failed to update verification status (deny) for ID: " . $verification['id']);
            throw new Exception("Failed to update verification status");
        }
        
        error_log("Login attempt denied for user ID: " . $verification['user_id']);
        
        $pdo->commit();
        
        // Send security alert email
        $subject = "Security Alert - Unauthorized Login Attempt";
        $body = "Hello,<br><br>"
              . "You have indicated that you did not attempt to log in to your Nexus E-Banking account.<br>"
              . "Login attempt details:<br>"
              . "IP Address: " . htmlspecialchars($verification['ip_address'] ?? 'Unknown') . "<br>"
              . "Browser: " . htmlspecialchars($verification['user_agent'] ?? 'Unknown') . "<br><br>"
              . "For your security, the login attempt has been blocked.<br><br>"
              . "If you did not attempt to log in, we recommend:<br>"
              . "1. Change your password immediately<br>"
              . "2. Contact our support team<br><br>"
              . "Thank you,<br>Nexus Bank";
        sendNotification($verification['email'], $subject, $body);

        header("Location: login.php?error=unauthorized_login");
        exit();
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Verification error: " . $e->getMessage());
    header("Location: login.php?error=verification_failed");
    exit();
}
?> 