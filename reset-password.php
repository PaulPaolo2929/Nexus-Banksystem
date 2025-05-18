<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

date_default_timezone_set('Asia/Manila'); // Same timezone as before

$token = $_GET['token'] ?? '';
if (!$token) {
    die("Missing token.");
}

// DEBUG
error_log("Checking token: $token at " . date('Y-m-d H:i:s'));

$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Invalid or expired reset token.");
}

$expires = strtotime($user['reset_expires_at']);
if ($expires < time()) {
    die("Reset token has expired.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($newPass !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!validatePassword($newPass)) {
        $error = "Password must meet strength requirements.";
    } else {
        $hashed = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires_at = NULL WHERE user_id = ?");
        $stmt->execute([$hashed, $user['user_id']]);

        $_SESSION['success'] = "Password successfully updated. Please log in.";
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Reset Password</title>
<link rel="stylesheet" href="./assets/css/reset-password.css">
</head>
<body>
  <div class="reset-page">
    <img src="./assets/images/Logo.png" alt="Nexus Logo" class="otp-logo">
    <div class="reset-card">
      <h2 class="reset-title">Reset Your Password</h2>
      <p class="reset-desc">Choose a strong and secure password you haven't used before.</p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
      <?php endif; ?>

      <form method="post">
        <input type="password" name="password" placeholder="New password" required />
        <input type="password" name="confirm_password" placeholder="Confirm password" required />
        <button type="submit">Reset Password</button>
      </form>

      <div class="reminder-note">
        <strong>Reminder:</strong> Your new password must be at least 8 characters, include uppercase and lowercase letters, and contain a number or special symbol for better security.
      </div>

      <a href="login.php" class="back-link">Back to Login</a>
    </div>
  </div>
</body>
</html>
