<?php
// Include necessary files and check if the user is an admin
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure the user is an admin
redirectIfNotAdmin();

// Fetch all user investments with plan and user details
$stmt = $pdo->query("SELECT ui.investment_id, ui.user_id, ui.amount, ui.status, ui.created_at, ui.matured_at, up.plan_name, up.interest_rate, u.full_name, u.email 
                     FROM investments ui
                     JOIN users u ON ui.user_id = u.user_id
                     JOIN investment_plans up ON ui.plan_id = up.plan_id");
$userInvestments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Investments Tracking - SecureBank Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>User Investments Tracking</h1>
            <a href="dashboard.php" class="back">Back to Dashboard</a>
        </header>

        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="manage-users.php">Manage Users</a>
            <a href="manage-loans.php">Manage Loans</a>
            <a href="manage_investments.php">Manage Investments</a>
            <a href="track-investments.php" class="active">Track Investments</a>
        </nav>

        <div class="content">
            <h2>User Investment Overview</h2>
            <?php if (empty($userInvestments)): ?>
                <p>No investments found.</p>
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
                                <td><?= htmlspecialchars($investment['full_name']) ?></td>
                                <td><?= htmlspecialchars($investment['email']) ?></td>
                                <td><?= htmlspecialchars($investment['plan_name']) ?></td>
                                <td>$<?= number_format($investment['amount'], 2) ?></td>
                                <td><?= number_format($investment['interest_rate'], 2) ?>%</td>
                                <td><?= htmlspecialchars($investment['status']) ?></td>
                                <td><?= !empty($investment['created_at']) ? date('M j, Y', strtotime($investment['created_at'])) : 'N/A' ?></td>
                                <td><?= !empty($investment['matured_at']) ? date('M j, Y', strtotime($investment['matured_at'])) : 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
