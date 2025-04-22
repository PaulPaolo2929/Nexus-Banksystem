<?php
function handleLogin($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT user_id, password_hash, is_admin FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    return ($user && password_verify($password, $user['password_hash'])) ? $user : false;
}
?>