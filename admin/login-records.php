<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

// Fetch all login records with user details
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
");
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

        </div>
    </main>
    </div>
</body>
</html>
