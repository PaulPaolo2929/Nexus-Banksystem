<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float) filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $toAccount = trim($_POST['to_account']);
    $description = trim($_POST['description']);

    if ($amount <= 0) {
        $error = "Amount must be greater than 0";
    } elseif (empty($toAccount)) {
        $error = "Recipient account number is required";
    } else {
        try {
            $pdo->beginTransaction();

            // Get sender's account
            $stmt = $pdo->prepare("SELECT account_id, balance, account_number FROM accounts WHERE user_id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $fromAccount = $stmt->fetch();

            // Get recipient's account
            $stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE account_number = ? FOR UPDATE");
            $stmt->execute([$toAccount]);
            $recipientAccount = $stmt->fetch();

            if ($fromAccount && $recipientAccount) {
                if ($fromAccount['account_number'] === $toAccount) {
                    $error = "Cannot transfer to your own account";
                } elseif ((float)$fromAccount['balance'] >= $amount) {
                    // Deduct from sender
                    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?");
                    $stmt->execute([$amount, $fromAccount['account_id']]);

                    // Add to recipient
                    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
                    $stmt->execute([$amount, $recipientAccount['account_id']]);

                    // Sender transaction
                    $stmt = $pdo->prepare("
                        INSERT INTO transactions (account_id, type, amount, description, related_account_id)
                        VALUES (?, 'transfer_out', ?, ?, ?)
                    ");
                    $stmt->execute([
                        $fromAccount['account_id'],
                        $amount,
                        $description ?: "Transfer to $toAccount",
                        $recipientAccount['account_id']
                    ]);

                    // Recipient transaction
                    $stmt = $pdo->prepare("
                        INSERT INTO transactions (account_id, type, amount, description, related_account_id)
                        VALUES (?, 'transfer_in', ?, ?, ?)
                    ");
                    $stmt->execute([
                        $recipientAccount['account_id'],
                        $amount,
                        $description ?: "Transfer from {$fromAccount['account_number']}",
                        $fromAccount['account_id']
                    ]);

                    $pdo->commit();
                    $success = "Successfully transferred $" . number_format($amount, 2) . " to account $toAccount";
                } else {
                    $error = "Insufficient funds";
                }
            } else {
                $error = "One or both accounts not found";
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Transfer failed: " . $e->getMessage();
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
    <title>SecureBank - Transfer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Transfer Funds</h1>
            <a href="../logout.php" class="logout">Logout</a>
        </header>

        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="deposit.php">Deposit</a>
            <a href="withdraw.php">Withdraw</a>
            <a href="transfer.php" class="active">Transfer</a>
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
                <p>Current Balance: <strong>$<?= number_format((float)$balance, 2) ?></strong></p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Recipient Account Number</label>
                    <input type="text" name="to_account" required>
                </div>

                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label>Description (Optional)</label>
                    <input type="text" name="description">
                </div>

                <button type="submit" class="btn">Transfer</button>
            </form>
        </div>
    </div>
</body>
</html>
