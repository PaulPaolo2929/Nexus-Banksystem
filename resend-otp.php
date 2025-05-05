<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/otp.php';

$type = $_GET['type'] ?? 'register';
$now = time();
$cooldownSeconds = 300; // 5 minutes

$email = null;

try {
    switch ($type) {
        case 'register':
            $email = $_SESSION['temp_email'] ?? null;
            break;

        case 'login':
            if (isset($_SESSION['temp_user_id'])) {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$_SESSION['temp_user_id']]);
                $user = $stmt->fetch();
                $email = $user['email'] ?? null;
            }
            break;

        case 'transfer':
            if (isset($_SESSION['user_id'])) {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();
                $email = $user['email'] ?? null;
            }
            break;
    }

    if (!$email) {
        $_SESSION['otp_error'] = "Session expired or invalid request. Please try again.";
        header("Location: otp-verification.php?type=$type");
        exit();
    }

    // Cooldown check
    $lastSent = $_SESSION['otp_last_sent'][$type] ?? 0;
    $timeSinceLast = $now - $lastSent;

    if ($timeSinceLast < $cooldownSeconds) {
        $remaining = $cooldownSeconds - $timeSinceLast;
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;
        $_SESSION['otp_error'] = "Please wait {$minutes}m {$seconds}s before requesting a new OTP.";
        header("Location: otp-verification.php?type=$type");
        exit();
    }

    // Resend OTP
    if (generateOTP($email)) {
        $_SESSION['otp_last_sent'][$type] = $now;
        $_SESSION['otp_success'] = "A new OTP has been sent to your email.";
    } else {
        $_SESSION['otp_error'] = "Failed to resend OTP. Please try again.";
        error_log("generateOTP() failed for email: $email");
    }

    header("Location: otp-verification.php?type=$type");
    exit();

} catch (Exception $e) {
    error_log("Resend OTP error: " . $e->getMessage());
    $_SESSION['otp_error'] = "An unexpected error occurred. Please try again.";
    header("Location: otp-verification.php?type=$type");
    exit();
}
