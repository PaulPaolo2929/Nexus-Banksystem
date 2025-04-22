<?php
// TEMPORARY DEBUGGING - Remove in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    if ($amount <= 0) {
        $error = "Amount must be greater than 0";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Get account
            $stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
            $stmt->execute([$userId]);
            $account = $stmt->fetch();
            
            if ($account) {
                // Update balance
                $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
                $stmt->execute([$amount, $account['account_id']]);
                
                // Record transaction
                $stmt = $pdo->prepare("
                    INSERT INTO transactions (account_id, type, amount, description)
                    VALUES (?, 'deposit', ?, ?)
                ");
                $stmt->execute([
                    $account['account_id'],
                    $amount,
                    "Cash deposit"
                ]);
                
                $pdo->commit();
                $success = "Successfully deposited $" . number_format($amount, 2);
            } else {
                $error = "Account not found";
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Transaction failed: " . $e->getMessage();
        }
    }
}

// Get current balance
$stmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Deposit</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Deposit Money</h1>
            <a href="../logout.php" class="logout">Logout</a>
        </header>
        
        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="deposit.php" class="active">Deposit</a>
            <a href="withdraw.php">Withdraw</a>
            <a href="transfer.php">Transfer</a>
            <a href="transactions.php">Transactions</a>
        </nav>
        
        <div class="content">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            
            <div class="balance-info">
                <p>Current Balance: <strong>$<?= number_format($balance, 2) ?></strong></p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Amount to Deposit</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
                </div>
                
                <button type="submit" class="btn">Deposit</button>
            </form>
        </div>
    </div>
</body>
</html>
