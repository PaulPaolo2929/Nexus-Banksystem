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
<head><title>Reset Password</title></head>
<body>
<h2>Reset Your Password</h2>
<?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
<form method="post">
    <input type="password" name="password" placeholder="New password" required>
    <input type="password" name="confirm_password" placeholder="Confirm password" required>
    <button type="submit">Reset Password</button>
</form>
</body>
</html>
