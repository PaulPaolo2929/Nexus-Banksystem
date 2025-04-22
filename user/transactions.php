<?php
// Show all errors (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];

// Get account ID
$stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();

if (!$account) {
    echo "<p>No account found for this user.</p>";
    exit();
}

$accountId = $account['account_id'];

// Get transactions
$stmt = $pdo->prepare("
    SELECT t.*, a.account_number as related_account_number
    FROM transactions t
    LEFT JOIN accounts a ON t.related_account_id = a.account_id
    WHERE t.account_id = ?
    ORDER BY t.created_at DESC
");
$stmt->execute([$accountId]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Transactions</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Transaction History</h1>
            <a href="../logout.php" class="logout">Logout</a>
        </header>
        
        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="deposit.php">Deposit</a>
            <a href="withdraw.php">Withdraw</a>
            <a href="transfer.php">Transfer</a>
            <a href="transactions.php" class="active">Transactions</a>
        </nav>
        
        <div class="content">
            <h2>All Transactions</h2>
            
            <?php if (empty($transactions)): ?>
                <p>No transactions found.</p>
            <?php else: ?>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Related Account</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?= date('M j, Y H:i', strtotime($txn['created_at'])) ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $txn['type'])) ?></td>
                                <td class="<?= in_array($txn['type'], ['deposit', 'transfer_in']) ? 'text-success' : 'text-danger' ?>">
                                    $<?= number_format($txn['amount'], 2) ?>
                                </td>
                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                <td><?= $txn['related_account_number'] ?: 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
