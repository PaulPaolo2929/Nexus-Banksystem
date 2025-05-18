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

function generateUniqueAccountNumber($pdo) {
    do {
        $number = generateAccountNumber();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE account_number = ?");
        $stmt->execute([$number]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);
    return $number;
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

function getRecentLoginRecords($pdo, $limit = 10) {
    $stmt = $pdo->prepare("
        SELECT 
            lr.login_time,
            lr.status,
            u.full_name,
            u.email
        FROM login_records lr
        JOIN users u ON u.user_id = lr.user_id
        ORDER BY lr.login_time DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
