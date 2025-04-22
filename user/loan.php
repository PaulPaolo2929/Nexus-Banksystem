<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user's loans
$stmt = $pdo->prepare("
    SELECT * FROM loans 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$loans = $stmt->fetchAll();

// Fetch balance from the accounts table (optional - not currently used in this page)
$accountStmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
$accountStmt->execute([$userId]);
$account = $accountStmt->fetch();
$balance = $account ? $account['balance'] : 0;

// Generate and store CSRF token to prevent double submissions
if (empty($_SESSION['loan_token'])) {
    $_SESSION['loan_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['loan_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['loan_token']) {
        $error = "Duplicate submission or invalid token.";
    } else {
        // Sanitize and validate input
        $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $term = intval($_POST['term']);
        $purpose = htmlspecialchars($_POST['purpose'], ENT_QUOTES, 'UTF-8');

        if ($amount < 100) {
            $error = "Minimum loan amount is $100";
        } elseif ($term < 1 || $term > 60) {
            $error = "Loan term must be between 1 and 60 months";
        } else {
            // Calculate interest rate
            $interestRate = 5.0;
            if ($amount > 10000) $interestRate = 4.5;
            if ($term > 36) $interestRate += 1.0;

            try {
                $stmt = $pdo->prepare("
                    INSERT INTO loans (user_id, amount, interest_rate, term_months, status, purpose)
                    VALUES (?, ?, ?, ?, 'pending', ?)
                ");
                $stmt->execute([$userId, $amount, $interestRate, $term, $purpose]);

                // Regenerate token to prevent double submissions
                $_SESSION['loan_token'] = bin2hex(random_bytes(32));

                $success = "Loan application submitted successfully!";
            } catch (Exception $e) {
                $error = "Failed to submit loan application: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Loans</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Loan Management</h1>
        <a href="../logout.php" class="logout">Logout</a>
    </header>

    <nav class="dashboard-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="deposit.php">Deposit</a>
        <a href="withdraw.php">Withdraw</a>
        <a href="transfer.php">Transfer</a>
        <a href="transactions.php">Transactions</a>
    </nav>

    <div class="content">
        <h2>Apply for a Loan</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <label>Loan Amount ($)</label>
                <input type="number" name="amount" min="100" step="100" required>
            </div>
            <div class="form-group">
                <label>Loan Term (months)</label>
                <input type="number" name="term" min="1" max="60" required>
            </div>
            <div class="form-group">
                <label>Purpose</label>
                <textarea name="purpose" required></textarea>
            </div>
            <button type="submit" class="btn">Apply for Loan</button>
        </form>

        <h2>Your Loans</h2>

        <?php if (empty($loans)): ?>
            <p>You have no active loans.</p>
        <?php else: ?>
            <table class="loans-table">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Interest Rate</th>
                        <th>Term</th>
                        <th>Status</th>
                        <th>Applied On</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td>$<?= number_format($loan['amount'], 2) ?></td>
                        <td><?= $loan['interest_rate'] ?>%</td>
                        <td><?= $loan['term_months'] ?> months</td>
                        <td class="status-<?= $loan['status'] ?>"><?= ucfirst($loan['status']) ?></td>
                        <td><?= date('M j, Y', strtotime($loan['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
