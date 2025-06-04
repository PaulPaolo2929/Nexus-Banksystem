<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$recordsPerPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

// Get filter parameters from GET or POST
$status = isset($_GET['status']) ? $_GET['status'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build SQL with filters
$sql = "
    SELECT lr.id, lr.ip_address, lr.user_agent, lr.status, lr.created_at,
           u.full_name, u.email
    FROM login_records lr
    JOIN users u ON u.user_id = lr.user_id
    WHERE lr.user_id = :user_id
";

$params = ['user_id' => $user_id];

if ($status !== '' && in_array($status, ['success', 'failed', 'pending'])) {
    $sql .= " AND lr.status = :status";
    $params['status'] = $status;
}

if ($start_date !== '') {
    $sql .= " AND lr.created_at >= :start_date";
    $params['start_date'] = $start_date . ' 00:00:00';
}

if ($end_date !== '') {
    $sql .= " AND lr.created_at <= :end_date";
    $params['end_date'] = $end_date . ' 23:59:59';
}

// Get total records count for pagination
$countSql = "
    SELECT COUNT(*) FROM login_records lr
    WHERE lr.user_id = :user_id
";

$countParams = ['user_id' => $user_id];

if ($status !== '' && in_array($status, ['success', 'failed', 'pending'])) {
    $countSql .= " AND lr.status = :status";
    $countParams['status'] = $status;
}

if ($start_date !== '') {
    $countSql .= " AND lr.created_at >= :start_date";
    $countParams['start_date'] = $start_date . ' 00:00:00';
}

if ($end_date !== '') {
    $countSql .= " AND lr.created_at <= :end_date";
    $countParams['end_date'] = $end_date . ' 23:59:59';
}

try {
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $recordsPerPage);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}

$sql .= " ORDER BY lr.created_at DESC LIMIT :limit OFFSET :offset";

try {
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $login_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'data' => $login_records,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
