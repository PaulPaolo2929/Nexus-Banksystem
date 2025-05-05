<?php
session_start();
require_once '../includes/db.php';

// Optional: for debugging if needed
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT type, SUM(amount) as total 
    FROM transactions 
    WHERE account_id = (SELECT account_id FROM accounts WHERE user_id = ?) 
    AND type IN ('deposit', 'transfer_out', 'withdrawal', 'loanpayment')
    GROUP BY type
");

$stmt->execute([$userId]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);
