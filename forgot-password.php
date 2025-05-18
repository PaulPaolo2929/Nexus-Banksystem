<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/send_reset_mail.php'; // Make sure this exists

date_default_timezone_set('Asia/Manila'); // Sync PHP timezone with your DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE user_id = ?")
            ->execute([$token, $expires, $user['user_id']]);

            $resetLink = "http://localhost/Nexus-Banksystem/reset-password.php?token=$token";

        
        if (sendResetLink($email, $resetLink)) {
            $_SESSION['success'] = "Password reset link sent to your email.";
        } else {
            $_SESSION['error'] = "Failed to send email.";
        }
    } else {
        $_SESSION['error'] = "Email not found.";
    }

    header('Location: forgot-password.php');
    exit;
}
?>
<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title>
<link rel="stylesheet" href="./assets/css/forgot-password.css">
</head>
<body>
  <div class="forgot-page">
     <img src="./assets/images/Logo.png" alt="Nexus Logo" class="otp-logo">
    <div class="forgot-card">
     
      <h2 class="forgot-title">Reset Your Password</h2>
      <p class="forgot-desc">
        Enter the email address associated with your account, and we'll send you a link to reset your password.
      </p>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
      <?php endif; ?>

      <form method="post">
        <input type="email" name="email" placeholder="Enter your email" required />
        <button type="submit">Send Reset Link</button>
      </form>

      <div class="reminder-note">
        <strong>Note:</strong> The reset link will expire in 1 hour for your security. If you don’t receive the email within a few minutes, please check your spam or junk folder.
      </div>

      <a href="login.php" class="back-link">Back to Login</a>
    </div>
  </div>
</body>
</html>
