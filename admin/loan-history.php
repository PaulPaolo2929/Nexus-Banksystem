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
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 0.75rem;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .back-link {
            margin-top: 2rem;
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout {
            float: right;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Loan History</h1>
        <a href="../logout.php" class="logout">Logout</a>
    </header>

    <nav class="dashboard-nav">
    <a href="dashboard.php" class="active">Dashboard</a>
            <a href="manage-users.php">Manage Users</a>
            <a href="manage-loans.php">Manage Loans</a>
            <a href="manage-investments.php">Manage Investments</a>
            <a href="track-investments.php">Users Investments</a>
            <a href="role.php">Roles</a>
            <a href="recent_transactions.php">Transactions</a>
            <a href="login-records.php">Login Records</a>
    </nav>

    <div class="content">

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
                            <td><?= $entry['loan_id'] ?></td>
                            <td><?= htmlspecialchars($entry['full_name']) ?></td>
                            <td><?= htmlspecialchars($entry['email']) ?></td>
                            <td>$<?= number_format($entry['amount'], 2) ?></td>
                            <td><?= $entry['interest_rate'] ?>%</td>
                            <td><?= htmlspecialchars($entry['purpose']) ?></td>
                            <td><?= ucfirst($entry['status']) ?></td>
                            <td><?= date('M d, Y - h:i A', strtotime($entry['changed_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="manage-loans.php" class="back-link">← Back to Manage Loans</a>
    </div>
</div>
</body>
</html>
