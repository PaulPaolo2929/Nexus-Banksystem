<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user's account and balance
$stmt = $pdo->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();
if (!$account) {
    die("User account not found.");
}
$accountId = $account['account_id'];
$balance = $account['balance'];

// Fetch unpaid and approved loans
$stmt = $pdo->prepare("
    SELECT * FROM loans 
    WHERE user_id = ? 
    AND status = 'approved' 
    AND is_paid = 'no'
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$loans = $stmt->fetchAll();

// CSRF token
if (empty($_SESSION['loan_payment_token'])) {
    $_SESSION['loan_payment_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['loan_payment_token'];

// Handle loan payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['loan_payment_token']) {
        $error = "Invalid or duplicate submission.";
    } else {
        $loanId = $_POST['loan_id'];
        $paymentAmount = filter_var($_POST['payment_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if ($paymentAmount <= 0) {
            $error = "Invalid payment amount.";
        } else {
            // Verify loan belongs to user
            $stmt = $pdo->prepare("SELECT * FROM loans WHERE loan_id = ? AND user_id = ?");
            $stmt->execute([$loanId, $userId]);
            $loan = $stmt->fetch();

            if (!$loan) {
                $error = "Loan not found.";
            } elseif ($loan['is_paid'] === 'yes') {
                $error = "Loan is already marked as paid.";
            } elseif ($paymentAmount > $loan['total_due']) {
                $error = "Payment exceeds total due.";
            } elseif ($balance < $paymentAmount) {
                $error = "Insufficient balance.";
            } else {
                try {
                    $pdo->beginTransaction();

                    // Deduct from account
                    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE user_id = ?");
                    $stmt->execute([$paymentAmount, $userId]);

                    $remaining = round($loan['total_due'] - $paymentAmount, 2);

                    if ($remaining > 0) {
                        $stmt = $pdo->prepare("UPDATE loans SET total_due = ? WHERE loan_id = ?");
                        $stmt->execute([$remaining, $loanId]);

                        $desc = "Partial Loan Payment";
                    } else {
                        $stmt = $pdo->prepare("UPDATE loans SET is_paid = 'yes', total_due = 0 WHERE loan_id = ?");
                        $stmt->execute([$loanId]);

                        $desc = "Full Loan Payment";

                        // Optional: delete loan after full payment
                        $stmt = $pdo->prepare("DELETE FROM loans WHERE loan_id = ?");
                        $stmt->execute([$loanId]);
                    }

                    // Log transaction
                    $stmt = $pdo->prepare("
                        INSERT INTO transactions (account_id, type, amount, description, related_account_id, created_at)
                        VALUES (?, 'loanpayment', ?, ?, NULL, ?)
                    ");
                    $stmt->execute([$accountId, $paymentAmount, $desc, date('Y-m-d H:i:s')]);

                    $pdo->commit();

                    $_SESSION['loan_payment_token'] = bin2hex(random_bytes(32));
                    $_SESSION['success_message'] = $desc . " successful.";
                    header("Location: loan-payment.php");
                    exit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Payment failed: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SecureBank - Loan Payment</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        <?php if (isset($_SESSION['success_message'])): ?>
        window.onload = function() {
            alert("<?= $_SESSION['success_message'] ?>");
            <?php unset($_SESSION['success_message']); ?>
        };
        <?php endif; ?>
    </script>
</head>
<body>
<div class="container">
    <header>
        <h1>Loan Payment</h1>
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
        <h2>Make a Loan Payment</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <label for="loan_id">Loan ID</label>
                <input type="number" name="loan_id" id="loan_id" required>
            </div>
            <div class="form-group">
                <label for="payment_amount">Payment Amount ($)</label>
                <input type="number" name="payment_amount" id="payment_amount" min="0.01" step="0.01" required>
            </div>
            <button type="submit" class="btn">Submit Payment</button>
        </form>

        <h2>Your Unpaid Loans</h2>

        <?php if (empty($loans)): ?>
            <p>You have no active loans.</p>
        <?php else: ?>
            <table class="loans-table">
                <thead>
                 <tr>
                    <th>Loan ID</th>
                    <th>Amount Due</th>
                    <th>Interest Rate</th>
                    <th>Term</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
                </thead>
                  <tbody>
                     <?php foreach ($loans as $loan): ?>
                         <tr>
                            <td><?= $loan['loan_id'] ?></td> <!-- Display Loan ID here -->
                            <td>$<?= number_format($loan['total_due'], 2) ?></td>
                            <td><?= $loan['interest_rate'] ?>%</td>
                            <td><?= $loan['term_months'] ?> months</td>
                            <td><?= $loan['is_paid'] === 'yes' ? 'Paid' : 'Active' ?></td>
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
