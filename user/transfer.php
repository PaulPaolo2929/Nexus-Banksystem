<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/otp.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';
$balance = 0;

// Get sender's account
$stmt = $pdo->prepare("SELECT * FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$fromAccount = $stmt->fetch();

if (!$fromAccount) {
    $error = "Your account could not be found.";
} else {
    $accountId = $fromAccount['account_id'];
    $accountNumber = $fromAccount['account_number'];

    // Get latest balance
    $stmt = $pdo->prepare("SELECT balance FROM accounts WHERE account_id = ?");
    $stmt->execute([$accountId]);
    $balance = $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toAccount = trim($_POST['to_account'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if ($amount <= 0) {
        $error = "Please enter a valid amount.";
    } elseif ($amount > $balance) {
        $error = "Insufficient balance for this transfer.";
    } elseif ($toAccount === $accountNumber) {
        $error = "You cannot transfer to your own account.";
    } else {
        // Check if recipient exists
        $stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE account_number = ?");
        $stmt->execute([$toAccount]);
        $recipientAccount = $stmt->fetch();

        if (!$recipientAccount) {
            $error = "Recipient account not found.";
        } else {
            // Get user's email
            $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $email = $user['email'] ?? '';

            if ($email && generateOTP($email)) {
                // Store the pending transfer in session for OTP verification
                $_SESSION['pending_transfer'] = [
                    'amount' => $amount,
                    'to_account' => $toAccount,
                    'description' => $description
                ];

                header("Location: ../otp-verification.php?type=transfer");
                exit();
            } else {
                $error = "Failed to send OTP. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SecureBank - Transfer Funds</title>
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
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
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

                <button type="submit" class="btn">Send OTP & Proceed</button>
            </form>
        </div>
    </div>
</body>
</html>
