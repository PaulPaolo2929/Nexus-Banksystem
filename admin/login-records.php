<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Count total login records
$totalCount = $pdo->query("SELECT COUNT(*) FROM login_records")->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

// Fetch paginated login records with user details
$stmt = $pdo->prepare("
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
    ORDER BY lr.created_at DESC
    LIMIT :perPage OFFSET :offset
");
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
