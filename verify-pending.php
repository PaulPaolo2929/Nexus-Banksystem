<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Set timezone to UTC for consistency
date_default_timezone_set('UTC');

// Cleanup expired tokens (older than 24 hours)
try {
    $stmt = $pdo->prepare("DELETE FROM login_verifications 
                          WHERE expires_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 24 HOUR)");
    $stmt->execute();
} catch (Exception $e) {
    error_log("Failed to cleanup expired tokens: " . $e->getMessage());
}

// Check if we have the necessary session data
if (!isset($_SESSION['login_verification_token'])) {
    header("Location: login.php");
    exit();
}

// Check if user is already verified (AJAX endpoint)
if (isset($_GET['check_status'])) {
    try {
        $token = $_SESSION['login_verification_token'];
        
        // Check if verification is complete (verified = 1)
        $stmt = $pdo->prepare("SELECT v.verified, v.user_id, u.is_admin 
                              FROM login_verifications v 
                              JOIN users u ON v.user_id = u.user_id 
                              WHERE v.token = ? AND v.expires_at > UTC_TIMESTAMP()");
        $stmt->execute([$token]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($verification) {
            if ($verification['verified'] == 1) {
                // Set up session if verified
                $_SESSION['user_id'] = $verification['user_id'];
                $_SESSION['is_admin'] = $verification['is_admin'];
                
                error_log("Verification status check: Verified for user ID " . $verification['user_id']);
                
                // Clear verification session data
                unset(
                    $_SESSION['login_verification_token'],
                    $_SESSION['login_verification_email'],
                    $_SESSION['temp_user_id'],
                    $_SESSION['temp_is_admin']
                );
                
                echo json_encode([
                    'verified' => true,
                    'redirect' => $verification['is_admin'] ? 'admin/dashboard.php' : 'user/dashboard.php'
                ]);
            } elseif ($verification['verified'] == 2) {
                // Verification was denied
                error_log("Verification status check: Denied for token " . $token);
                
                // Clear all session data for denied verification
                session_destroy();
                session_start();
                
                echo json_encode([
                    'verified' => false,
                    'redirect' => 'login.php?error=unauthorized_login'
                ]);
            } else {
                // Still pending
                error_log("Verification status check: Still pending for token " . $token);
                echo json_encode(['verified' => false]);
            }
        } else {
            // Token expired or invalid
            error_log("Verification status check: Token expired or invalid for token " . $token);
            echo json_encode([
                'verified' => false,
                'redirect' => 'login.php?error=verification_expired'
            ]);
        }
    } catch (Exception $e) {
        error_log("Verification status check error: " . $e->getMessage());
        echo json_encode(['verified' => false, 'error' => true]);
    }
    exit();
}

// Check if already verified on page load
try {
    $token = $_SESSION['login_verification_token'];
    $stmt = $pdo->prepare("SELECT verified, user_id FROM login_verifications WHERE token = ? AND expires_at > UTC_TIMESTAMP()");
    $stmt->execute([$token]);
    $verification = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($verification) {
        if ($verification['verified'] == 1) {
            error_log("Initial verification check: Already verified for user ID " . $verification['user_id']);
            header("Location: " . ($_SESSION['is_admin'] ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit();
        } elseif ($verification['verified'] == 2) {
            error_log("Initial verification check: Already denied for token " . $token);
            header("Location: login.php?error=unauthorized_login");
            exit();
        }
    }
} catch (Exception $e) {
    error_log("Initial verification check error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Pending - Nexus E-Banking</title>
    <link rel="stylesheet" href="./assets/css/main.css">
    <style>
        .pending-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 30px;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn-cancel {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .error-message {
            color: #f44336;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="pending-container">
        <img src="./assets/images/Logo.png" alt="Nexus Logo" class="logo">
        <h2>Verification Pending</h2>
        <div class="spinner"></div>
        <p>We've sent a verification email to your registered email address.</p>
        <p>Please check your email and click the appropriate button to verify your login attempt.</p>
        <p><small>This page will automatically redirect you once you verify through the email.</small></p>
        <p class="error-message" id="errorMessage">There was an error checking verification status. Please try again.</p>
        <a href="login.php" class="btn-cancel">Cancel Login</a>
    </div>

    <script>
        let errorCount = 0;
        const maxErrors = 3;
        const errorMessage = document.getElementById('errorMessage');

        // Check verification status every 2 seconds
        function checkVerificationStatus() {
            fetch('verify-pending.php?check_status=1')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        errorCount++;
                        if (errorCount >= maxErrors) {
                            errorMessage.style.display = 'block';
                        }
                    } else {
                        errorCount = 0;
                        errorMessage.style.display = 'none';
                        if (data.verified || data.redirect) {
                            window.location.href = data.redirect || (data.verified ? 'user/dashboard.php' : 'login.php');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorCount++;
                    if (errorCount >= maxErrors) {
                        errorMessage.style.display = 'block';
                    }
                });
        }

        // Start checking status
        setInterval(checkVerificationStatus, 2000);
        // Check immediately on page load
        checkVerificationStatus();
    </script>
</body>
</html> 