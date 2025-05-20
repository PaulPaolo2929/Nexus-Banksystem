<?php 
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/otp.php';

$error = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'unauthorized_login':
            $error = "Login attempt was denied. If this wasn't you, please change your password.";
            break;
        case 'verification_failed':
            $error = "Verification failed. Please try logging in again.";
            break;
        case 'verification_expired':
            $error = "Verification expired. Please try logging in again.";
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
    $password = $_POST['password'] ?? '';

    try {
        // Clear any existing session data
        session_unset();
        
        // Case-insensitive email match
        $stmt = $pdo->prepare("SELECT user_id, password_hash, is_admin, status, is_active FROM users WHERE LOWER(email) = ?");
        $stmt->execute([strtolower($email)]);
        $user = $stmt->fetch();

        // Get IP address and user agent
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        if ($user) {
            if ($user['status'] !== 'approved') {
                $error = "Your account is still pending approval.";
                // Log failed login
                $stmtLog = $pdo->prepare("INSERT INTO login_records (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)");
                $stmtLog->execute([$user['user_id'], $ipAddress, $userAgent, 'failed']);
            } elseif ($user['is_active'] == 0) {
                $error = "Your account is deactivated. Please contact the admin.";
                // Log failed login
                $stmtLog = $pdo->prepare("INSERT INTO login_records (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)");
                $stmtLog->execute([$user['user_id'], $ipAddress, $userAgent, 'failed']);
            } elseif (password_verify($password, $user['password_hash'])) {
                // Log successful login
                $stmtLog = $pdo->prepare("INSERT INTO login_records (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)");
                $stmtLog->execute([$user['user_id'], $ipAddress, $userAgent, 'success']);

                // Store temporary session data
                $_SESSION['temp_user_id'] = $user['user_id'];
                $_SESSION['temp_is_admin'] = $user['is_admin'];
                
                // Send OTP
                if (generateOTP($email)) {
                    header("Location: otp-verification.php?type=login");
                    exit();
                } else {
                    $error = "Failed to send OTP. Please try again.";
                }
            } else {
                $error = "Invalid email or password.";
                // Log failed login
                $stmtLog = $pdo->prepare("INSERT INTO login_records (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, ?)");
                $stmtLog->execute([$user['user_id'], $ipAddress, $userAgent, 'failed']);
            }
        } else {
            $error = "User account not found. Please double-check your email.";
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $error = "System error. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Bank - Login</title>
    <link rel="stylesheet" href="./assets/css/login.css">
</head>
<body>
  <div class="wrapper">
    <div class="left-panel">
        <div>
        <img src="./assets/images/Logo.png" alt="Nexus Logo" class="logo" />
        </div>
        <div class="handshake-container">
            <img src="./assets/images/handshake.png" alt="Handshake" class="handshake" />
        </div>
    
      <div class="content">
        <h2 class="headline">Partnership for<br>Business Growth</h2>
        <p class="description">
          Welcome to Nexus Bank System, your trusted partner in secure and efficient banking solutions.
        </p>
      </div>
    </div>

    <div class="container" >
        <div class="login-form">
                 <p style="text-align: start;"> Welcome Back </p> 
                <h1 style="text-align: start;">Login to your Account</h1>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                    </div>

                    <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                    </div>
                    <p style="text-align: end; margin-top: 0; margin-bottom: 0.5rem; "><a href="forgot-password.php">Forgot your password?</a></p>
                    <button type="submit" class="btn">LOGIN</button>
                </form>
            
                
                <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
  </div>
</body>
</html>
