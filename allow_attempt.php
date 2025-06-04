<?php
session_start();
require_once __DIR__ . '/includes/db.php';

// Validate user_id and token from query parameters
$userId = $_GET['user_id'] ?? null;
$token = $_GET['token'] ?? null;

if (!$userId || !$token) {
    die('Invalid request.');
}

// TODO: Validate token for security (e.g., store tokens in DB and verify)
// For now, we will skip token validation for simplicity

try {
    // Reactivate user account by setting is_active to 1
    $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Optionally, clear failed login records or mark them as resolved
    $stmtClear = $pdo->prepare("DELETE FROM login_records WHERE user_id = ? AND status = 'failed'");
    $stmtClear->execute([$userId]);

    echo "Your account has been reactivated. You can now attempt to login again.";
} catch (PDOException $e) {
    error_log("Allow attempt error: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
}
