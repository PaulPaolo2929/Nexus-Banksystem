<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Redirect if not admin
redirectIfNotAdmin();

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Count total loan history records
$totalCount = $pdo->query("SELECT COUNT(*) FROM loan_history")->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

// Fetch loan history records with loan and user info (paginated)
$stmt = $pdo->prepare("
    SELECT lh.*, l.amount, l.interest_rate, l.purpose, u.full_name, u.email 
    FROM loan_history lh
    JOIN loans l ON lh.loan_id = l.loan_id
    JOIN users u ON l.user_id = u.user_id
    ORDER BY lh.changed_at DESC
    LIMIT :perPage OFFSET :offset
");
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$loanHistory = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan History - SecureBank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-loan-history.css">

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
                                <a href="loan-history.php" class="btn dash-text">Loan History</a>
                                <a href="login-records.php" class="btn">Login Records</a>
                                <a href="manage-messages.php" class="btn">Contact Messages</a>
                            </nav>

                             <div class="logout-cont">
                                <a href="../logout.php" class="logout">Logout</a>
                            </div>
                </aside>

<main class="container">
    <header>
        <h1>Loan History</h1>
        <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
    </header>

    <div class="content">
        <h1>Loan History</h1>
        <?php if (empty($loanHistory)): ?>
            <p>No loan history found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Interest</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Changed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loanHistory as $entry): ?>
                        <tr>
                            <td data-label="Loan ID"><?= $entry['loan_id'] ?></td>
                            <td data-label="User"><?= htmlspecialchars($entry['full_name']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($entry['email']) ?></td>
                            <td data-label="Amount">$<?= number_format($entry['amount'], 2) ?></td>
                            <td data-label="Interest"><?= $entry['interest_rate'] ?>%</td>
                            <td data-label="Purpose"><?= htmlspecialchars($entry['purpose']) ?></td>
                            <td data-label="Status"><?= ucfirst($entry['status']) ?></td>
                            <td data-label="Changed At"><?= date('M d, Y - h:i A', strtotime($entry['changed_at'])) ?></td>
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
