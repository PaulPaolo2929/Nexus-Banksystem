<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get user account information
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.*, a.account_number, a.balance 
    FROM users u 
    JOIN accounts a ON u.user_id = a.user_id 
    WHERE u.user_id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die('User account not found.');
}

// Get recent transactions
$stmt = $pdo->prepare("
    SELECT * FROM transactions 
    WHERE account_id = (SELECT account_id FROM accounts WHERE user_id = ?)
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>
            <a href="../logout.php" class="logout">Logout</a>
        </header>
        
        <div class="dashboard">
            <div class="account-summary">
                <h2>Account Summary</h2>
                <p>Account Number: <?= htmlspecialchars($user['account_number']) ?></p>
                <p class="balance">Balance: $<?= number_format($user['balance'], 2) ?></p>
            </div>
            
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="deposit.php" class="btn">Deposit Money</a>
                    <a href="withdraw.php" class="btn">Withdraw Funds</a>
                    <a href="transfer.php" class="btn">Transfer Funds</a>
                    <a href="investment.php" class="btn">Investment</a>
                    <a href="transactions.php" class="btn">View Transactions</a>
                    <a href="loan.php" class="btn">Apply for Loan</a>
                    <a href="loan-payment.php" class="btn">Pay Loan</a>
                </div>
            </div>
            
            <div class="recent-transactions">
                <h2>Recent Transactions</h2>
                <?php if (empty($transactions)): ?>
                    <p>No transactions found.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $txn): ?>
                                <tr>
                                    <td><?= date('M j, Y', strtotime($txn['created_at'])) ?></td>
                                    <td><?= ucfirst($txn['type']) ?></td>
                                    <td class="<?= in_array($txn['type'], ['deposit', 'transfer_in']) ? 'text-success' : 'text-danger' ?>">
                                        $<?= number_format($txn['amount'], 2) ?>
                                    </td>
                                    <td><?= htmlspecialchars($txn['description']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="transactions.php" class="view-all">View All Transactions</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
