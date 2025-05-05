<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'];

$query = "
    SELECT 
        DAYNAME(created_at) as day,
        SUM(CASE WHEN LOWER(type) = 'deposit' THEN amount ELSE 0 END) as total_deposit,
        SUM(CASE WHEN LOWER(type) = 'withdrawal' THEN amount ELSE 0 END) as total_withdraw
    FROM transactions
    WHERE account_id = (SELECT account_id FROM accounts WHERE user_id = ?)
      AND created_at >= DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
      AND created_at < DATE_ADD(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 7 DAY)
    GROUP BY DAYOFWEEK(created_at)
    ORDER BY FIELD(DAYNAME(created_at), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
";

$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);

