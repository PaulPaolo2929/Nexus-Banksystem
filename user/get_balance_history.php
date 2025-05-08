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

// Fetch balance history for the logged-in user
$stmt = $pdo->prepare("
    SELECT last_updated, total_balance 
    FROM Balance 
    WHERE user_id = ? 
    ORDER BY last_updated ASC
");
$stmt->execute([$userId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format data for ApexCharts (datetime and value)
$formatted = [];
foreach ($data as $row) {
    $formatted[] = [
        'x' => date('c', strtotime($row['last_updated'])), // ISO format datetime
        'y' => (float) $row['total_balance']
    ];
}

header('Content-Type: application/json');
echo json_encode($formatted);
