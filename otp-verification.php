<?php
// Enable error reporting at the VERY TOP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session before any output
session_start();

// Absolute paths for includes
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/otp.php';

// Debugging: Check session status
error_log("Session status: " . session_status());
error_log("Session data: " . print_r($_SESSION, true));

// Redirect if not in OTP flow
if (!isset($_SESSION['temp_email']) && !isset($_SESSION['temp_user_id'])) {
    error_log("Redirecting to login - no temp session");
    header("Location: login.php");
    exit();
}

// Initialize variables
$error = '';
$type = $_GET['type'] ?? 'register'; // Default to register

// Process OTP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedOTP = $_POST['otp'] ?? '';
    
    try {
        if ($type === 'register') {
            // Registration OTP verification
            $email = $_SESSION['temp_email'] ?? null;
            
            if (!$email || !verifyOTP($email, $submittedOTP)) {
                $error = "Invalid OTP or session expired";
            } else {
                // Get user from database
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Create bank account
                    $accountNumber = generateAccountNumber();
                    $pdo->prepare("INSERT INTO accounts (user_id, account_number) VALUES (?, ?)")
                        ->execute([$user['user_id'], $accountNumber]);
                    
                    // Set session and cleanup
                    $_SESSION['user_id'] = $user['user_id'];
                    unset($_SESSION['temp_email']);
                    
                    header("Location: user/dashboard.php");
                    exit();
                } else {
                    $error = "User registration incomplete";
                }
            }
        } elseif ($type === 'login') {
            // Login OTP verification
            $user_id = $_SESSION['temp_user_id'] ?? null;
            $is_admin = $_SESSION['temp_is_admin'] ?? false;
            
            if (!$user_id) {
                $error = "Session expired. Please login again.";
            } else {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if ($user && verifyOTP($user['email'], $submittedOTP)) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['is_admin'] = $is_admin;
                    unset($_SESSION['temp_user_id']);
                    unset($_SESSION['temp_is_admin']);
                    
                    header("Location: " . ($is_admin ? "admin/dashboard.php" : "user/dashboard.php"));
                    exit();
                } else {
                    $error = "Invalid OTP";
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $error = "System error. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - SecureBank</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Verify Your Identity</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="otp-instructions">
            <p>We've sent a 6-digit code to your <?= $type === 'register' ? 'registered' : 'account' ?> email address.</p>
            <p>Please check your inbox and enter the code below:</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="otp">Verification Code</label>
                <input type="text" 
                       id="otp" 
                       name="otp" 
                       required
                       pattern="\d{6}" 
                       title="6-digit code"
                       inputmode="numeric"
                       autocomplete="one-time-code">
            </div>
            
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
        
        <div class="otp-help">
            <p>Didn't receive the code? <a href="resend-otp.php?type=<?= $type ?>">Resend Code</a></p>
            <p>Check your spam folder if you don't see the email.</p>
        </div>
    </div>
</body>
</html>