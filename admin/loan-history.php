<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Redirect if not admin
redirectIfNotAdmin();

// Fetch loan history records with loan and user info
$stmt = $pdo->prepare("
    SELECT lh.*, l.amount, l.interest_rate, l.purpose, u.full_name, u.email 
    FROM loan_history lh
    JOIN loans l ON lh.loan_id = l.loan_id
    JOIN users u ON l.user_id = u.user_id
    ORDER BY lh.changed_at DESC
");
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

        
    </div>
</main>
</div>
</body>
</html>
