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
    // Deactivate user account by setting is_active to 0
    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo "Your account has been deactivated due to suspicious login attempts. Please contact support to reactivate.";
} catch (PDOException $e) {
    error_log("Report suspicious error: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
}
?>
