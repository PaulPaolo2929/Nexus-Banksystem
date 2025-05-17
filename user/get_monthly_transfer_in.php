<?php
session_start();
require_once '../includes/db.php'; // adjust if needed

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch the user's account_id
$stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    echo json_encode(['error' => 'Account not found']);
    exit();
}

$accountId = $account['account_id'];

// Get monthly deposit totals
$query = "
  SELECT 
    DATE_FORMAT(created_at, '%Y-%m') AS month,
    SUM(amount)                 AS total_deposit
  FROM transactions
  WHERE type = 'transfer_in'
    AND account_id = (
      SELECT account_id 
      FROM accounts 
      WHERE user_id = ?           -- now binding $userId 
    )
  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
  ORDER BY month ASC
";

$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);             // bind the user_id here
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
