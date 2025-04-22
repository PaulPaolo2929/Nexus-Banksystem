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

        $resetLink = "http://localhost/securebank/reset-password.php?token=$token";
        
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
<head><title>Forgot Password</title></head>
<body>
<h2>Forgot Password</h2>
<?php if (!empty($_SESSION['error'])): ?><p style="color:red"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p><?php endif; ?>
<?php if (!empty($_SESSION['success'])): ?><p style="color:green"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p><?php endif; ?>
<form method="post">
    <input type="email" name="email" placeholder="Your email" required>
    <button type="submit">Send Reset Link</button>
</form>
<a href="login.php">Back to Login</a>
</body>
</html>
