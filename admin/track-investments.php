<?php
// Include necessary files and check if the user is an admin
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure the user is an admin
redirectIfNotAdmin();

// Fetch all user investments with plan and user details (paginated)
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
$totalCount = $pdo->query("SELECT COUNT(*) FROM investments")->fetchColumn();
$totalPages = ceil($totalCount / $perPage);
$stmt = $pdo->prepare("SELECT ui.investment_id, ui.user_id, ui.amount, ui.status, ui.created_at, ui.matured_at, up.plan_name, up.interest_rate, u.full_name, u.email 
                     FROM investments ui
                     JOIN users u ON ui.user_id = u.user_id
                     JOIN investment_plans up ON ui.plan_id = up.plan_id
                     LIMIT :perPage OFFSET :offset");
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$userInvestments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Investments Tracking - Nexus Bank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-track-investment.css">

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
                                <a href="track-investments.php" class="btn dash-text">Users Investments</a>
                                <a href="role.php" class="btn">Roles</a>
                                <a href="recent_transactions.php" class="btn">Transactions</a>
                                <a href="recent_transactions.php" class="btn">Loan History</a>
                                <a href="login-records.php" class="btn">Login Records</a>
                                <a href="manage-messages.php" class="btn">Contact Messages</a>
                            </nav>

                             <div class="logout-cont">
                                <a href="../logout.php" class="logout">Logout</a>
                            </div>
                </aside>


    <main class="container">
        <header>
            <h1>User Investments Tracking</h1>
            <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>

        <div class="content">
            <h2>User Investment Overview</h2>
            <?php if (empty($userInvestments)): ?>
                <p>No investments found.</p>
                <div class="table-cont">
            <?php else: ?>
                <table class="user-investments-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Investment Plan</th>
                            <th>Amount Invested</th>
                            <th>Interest Rate</th>
                            <th>Status</th>
                            <th>Invested Date</th>
                            <th>Matured Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userInvestments as $investment): ?>
                            <tr>
                                <td data-label="User"><?= htmlspecialchars($investment['full_name']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($investment['email']) ?></td>
                                <td data-label="Investment Plan"><?= htmlspecialchars($investment['plan_name']) ?></td>
                                <td data-label="Amount Invested">₱<?= number_format($investment['amount'], 2) ?></td>
                                <td data-label="Interest Rate"><?= number_format($investment['interest_rate'], 2) ?>%</td>
                                <td data-label="Status"><?= htmlspecialchars($investment['status']) ?></td>
                                <td data-label="Invested Date"><?= !empty($investment['created_at']) ? date('M j, Y', strtotime($investment['created_at'])) : 'N/A' ?></td>
                                <td data-label="Matured Date"><?= !empty($investment['matured_at']) ? date('M j, Y', strtotime($investment['matured_at'])) : 'N/A' ?></td>
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
        </div>
    </main>
    </div>
</body>
</html>
