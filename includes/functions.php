<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}


function validatePassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

// Add other essential functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// Add all other original functions here
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

function redirectIfNotAdmin() {
    redirectIfNotLoggedIn();
    if (!isAdmin()) {
        header("Location: ../user/dashboard.php");
        exit();
    }
}

function generateAccountNumber() {
    return 'SB' . str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
}


function formatCurrency($amount) {
    return number_format($amount, 2, '.', ',');
}

function formatDate($dateString) {
    return date('M j, Y H:i', strtotime($dateString));
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>