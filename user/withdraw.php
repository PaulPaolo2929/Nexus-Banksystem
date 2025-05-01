<?php
// TEMPORARY DEBUGGING - REMOVE IN PRODUCTION
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

            // Get account with balance
            $stmt = $pdo->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $account = $stmt->fetch();

            if ($account) {
                if ($account['balance'] >= $amount) {
                    // Update balance
                    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
                    $stmt->execute([$amount, $account['account_id']]);

                    // Record transaction
                    $stmt = $pdo->prepare("
                        INSERT INTO transactions (account_id, type, amount, description)
                        VALUES (?, 'withdrawal', ?, ?)
                    ");
                    $stmt->execute([
                        $account['account_id'],
                        $amount,
                        "Cash withdrawal"
                    ]);

                    $pdo->commit();
                    $success = "Successfully withdrew $" . number_format($amount, 2);
                } else {
                    $error = "Insufficient funds";
                }
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
    <title>SecureBank - Withdraw</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Withdraw Funds</h1>
            <a href="../logout.php" class="logout">Logout</a>
        </header>

        <nav class="dashboard-nav">
            <a href="deposit.php" class="btn">Deposit Money</a>
            <a href="withdraw.php" class="btn">Withdraw Funds</a>
            <a href="transfer.php" class="btn">Transfer Funds</a>
            <a href="investment.php" class="btn">Investment</a>
            <a href="transactions.php" class="btn">View Transactions</a>
            <a href="loan.php" class="btn">Apply for Loan</a>
            <a href="loan-payment.php" class="btn">Pay Loan</a>
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
                    <label>Amount to Withdraw</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
                </div>

                <button type="submit" class="btn">Withdraw</button>
            </form>
        </div>
    </div>
</body>
</html>
