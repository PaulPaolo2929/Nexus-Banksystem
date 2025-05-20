<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session at the beginning (added to avoid any issues with session usage)
session_start();

// Include database and functions
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admins can access the page
redirectIfNotAdmin();

// Get system statistics
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAccounts = $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
$totalBalance = $pdo->query("SELECT SUM(balance) FROM accounts")->fetchColumn();
$totalBalance = $totalBalance ?: 0;
$pendingLoans = $pdo->query("SELECT COUNT(*) FROM loans WHERE status = 'pending'")->fetchColumn();

// Get recent users
$recentUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">

       <script src="../assets/js/sidebar.js"></script>
</head>
<body>

<div class="wrapper">
        <aside class="sidebar">
            
                <div class="Logos-cont">
                    <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
                </div>

                <nav class="dashboard-nav">
                    <a href="dashboard.php" class="active btn dash-text">Dashboard</a>
                    <a href="manage-users.php" class="btn">Manage Users</a>
                    <a href="manage-loans.php" class="btn">Manage Loans</a>
                    <a href="manage-investments.php" class="btn">Manage Investments</a>
                    <a href="track-investments.php" class="btn">Users Investments</a>
                    <a href="role.php" class="btn">Roles</a>
                    <a href="recent_transactions.php" class="btn">Transactions</a>
                    <a href="loan-history.php" class="btn">Loan History</a>
                    <a href="login-records.php" class="btn">Login Records</a>
                    <a href="manage-messages.php" class="btn">Contact Messages</a>
                </nav>

                    <div class="logout-cont">
                        <a href="../logout.php" class="logout">Logout</a>
                    </div>
    </aside>


    <main class="container">
        <header>
            <h1>Admin Dashboard</h1>
           <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>     
        
        <div class="content">
            <h2>System Overview</h2>
            
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p><?= $totalUsers ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Accounts</h3>
                    <p><?= $totalAccounts ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Balance</h3>
                    <p>$<?= number_format($totalBalance, 2) ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Pending Loans</h3>
                    <p><?= $pendingLoans ?></p>
                </div>
            </div>
            
            <h2>Recent Users</h2>
            <div class="table-cont">
            <?php if (empty($recentUsers)): ?>
                <p>No users found.</p>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joined On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <!-- Inside your Admin Dashboard (dashboard.php) -->
</div>

</body>
</html>