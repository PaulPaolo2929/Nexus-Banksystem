<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

$searchName = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';
$startDate = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$endDate = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Build count query with filters
$countSql = "SELECT COUNT(*) FROM login_records lr JOIN users u ON u.user_id = lr.user_id WHERE 1=1";
$countParams = [];

if ($searchName !== '') {
    $countSql .= " AND u.full_name LIKE :searchName";
    $countParams[':searchName'] = '%' . $searchName . '%';
}

if ($statusFilter !== '' && in_array($statusFilter, ['success', 'failed', 'pending'])) {
    $countSql .= " AND lr.status = :status";
    $countParams[':status'] = $statusFilter;
}

if ($startDate !== '') {
    $countSql .= " AND lr.created_at >= :startDate";
    $countParams[':startDate'] = $startDate . ' 00:00:00';
}

if ($endDate !== '') {
    $countSql .= " AND lr.created_at <= :endDate";
    $countParams[':endDate'] = $endDate . ' 23:59:59';
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($countParams);
$totalCount = $countStmt->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

// Build main query with filters
$sql = "
    SELECT 
        lr.id,
        lr.user_id,
        u.full_name,
        u.email,
        lr.ip_address,
        lr.user_agent,
        lr.status,
        lr.created_at
    FROM login_records lr
    JOIN users u ON u.user_id = lr.user_id
    WHERE 1=1
";

$params = [];

if ($searchName !== '') {
    $sql .= " AND u.full_name LIKE :searchName";
    $params[':searchName'] = '%' . $searchName . '%';
}

if ($statusFilter !== '' && in_array($statusFilter, ['success', 'failed', 'pending'])) {
    $sql .= " AND lr.status = :status";
    $params[':status'] = $statusFilter;
}

if ($startDate !== '') {
    $sql .= " AND lr.created_at >= :startDate";
    $params[':startDate'] = $startDate . ' 00:00:00';
}

if ($endDate !== '') {
    $sql .= " AND lr.created_at <= :endDate";
    $params[':endDate'] = $endDate . ' 23:59:59';
}

$sql .= " ORDER BY lr.created_at DESC LIMIT :perPage OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$loginRecords = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Login Records - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-login-records.css">

       <script src="../assets/js/sidebar.js"></script>
</head>
<body>


 <div class="wrapper">
            <aside class="sidebar">
                        
                            <div class="Logos-cont">
                                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
                            </div>

                            <nav class="dashboard-nav">
                                <a href="dashboard.php" class="active btn ">Dashboard</a>
                                <a href="manage-users.php" class="btn ">Manage Users</a>
                                <a href="manage-loans.php" class="btn">Manage Loans</a>
                                <a href="manage-investments.php" class="btn">Manage Investments</a>
                                <a href="track-investments.php" class="btn">Users Investments</a>
                                <a href="role.php" class="btn">Roles</a>
                                <a href="recent_transactions.php" class="btn">Transactions</a>
                                <a href="loan-history.php" class="btn">Loan History</a>
                                <a href="login-records.php" class="btn dash-text">Login Records</a>
                                <a href="manage-messages.php" class="btn">Contact Messages</a>
                            </nav>

                             <div class="logout-cont">
                                <a href="../logout.php" class="logout">Logout</a>
                            </div>
                </aside>

    <main class="container">
        <header>
            <h1>All Login Records</h1>
        <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>
        
    <div class="content">
        <h1>Account Login Records</h1>

        <form method="GET" class="filter-form">
            <label for="search_name">User Name:</label>
            <input type="text" id="search_name" name="search_name" value="<?= htmlspecialchars($searchName) ?>" placeholder="Search by user name">

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="">All</option>
                <option value="success" <?= $statusFilter === 'success' ? 'selected' : '' ?>>Success</option>
                <option value="failed" <?= $statusFilter === 'failed' ? 'selected' : '' ?>>Failed</option>
                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
            </select>

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <?php if (empty($loginRecords)): ?>
            <p>No login records found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Login Time</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loginRecords as $record): ?>
                        <tr>
                            <td data-label="ID"><?= htmlspecialchars($record['id']) ?></td>
                            <td data-label="User"><?= htmlspecialchars($record['full_name']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($record['email']) ?></td>
                            <td data-label="Status" class="status-<?= htmlspecialchars($record['status']) ?>">
                                <?= ucfirst(htmlspecialchars($record['status'])) ?>
                            </td>
                            <td data-label="Login Time"><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($record['created_at']))) ?></td>
                            <td data-label="IP Address"><?= htmlspecialchars($record['ip_address']) ?></td>
                            <td data-label="User Agent"><?= htmlspecialchars($record['user_agent']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
        <style>
        .pagination { text-align: center; margin: 20px 0; }
        .pagination a { display: inline-block; margin: 0 4px; padding: 6px 12px; color: #007bff; background: #fff; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; transition: background 0.2s, color 0.2s; }
        .pagination a.btn-primary, .pagination a.active { background: #007bff; color: #fff; border-color: #007bff; pointer-events: none; }
        .pagination a:hover:not(.btn-primary):not(.active) { background: #f0f0f0; }
        </style>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'btn-primary active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        </div>
    </main>
    </div>
</body>
</html>
