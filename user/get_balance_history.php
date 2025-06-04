<?php
session_start();
require_once '../includes/db.php';

// Set timezone to Philippine time
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

// Get the user's account ID
$stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();

if (!$account) {
    http_response_code(404);
    echo json_encode(['error' => 'Account not found']);
    exit;
}

$accountId = $account['account_id'];

// Get all transactions for the account
$stmt = $pdo->prepare("
    SELECT created_at, 
           CASE 
               WHEN type IN ('deposit', 'transfer_in') THEN amount
               WHEN type IN ('withdrawal', 'transfer_out', 'loanpayment') THEN -amount
               ELSE 0
           END as amount_change
    FROM transactions 
    WHERE account_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$accountId]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate running balance
$balance = 0;
$balanceHistory = [];

foreach ($transactions as $txn) {
    $balance += $txn['amount_change'];
    $balanceHistory[] = [
        'x' => date('c', strtotime($txn['created_at'])), // ISO format datetime
        'y' => (float) $balance
    ];
}

header('Content-Type: application/json');
echo json_encode($balanceHistory);
