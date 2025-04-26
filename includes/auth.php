<?php
function handleLogin($email, $password) {
    global $pdo;
    
    // Query to fetch user data including status and is_admin
    $stmt = $pdo->prepare("SELECT user_id, password_hash, is_admin, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Check if user exists, password matches, and account is approved
    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['status'] !== 'approved') {
            return 'Your account is not approved yet.'; // You can handle the error message here
        }
        return $user; // Return user data if login is successful
    }
    return false; // Return false if login fails
}
?>
