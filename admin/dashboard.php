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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <a href="../logout.php" class="logout">Logout</a>
        </header>
        
        <nav class="dashboard-nav">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="manage-users.php">Manage Users</a>
            <a href="manage-loans.php">Manage Loans</a>
        </nav>
        
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
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
