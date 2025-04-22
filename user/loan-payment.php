<?php
// Include necessary files
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Redirect if not logged in
redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user's loans that are unpaid
$stmt = $pdo->prepare("SELECT * FROM loans WHERE user_id = ? AND is_paid = 0 ORDER BY created_at DESC");
$stmt->execute([$userId]);
$loans = $stmt->fetchAll();

// Fetch the user's balance and account_id from accounts table
$stmt = $pdo->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();

if (!$account) {
    die('Account not found.');
}

$balance = $account['balance'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loanId = $_POST['loan_id'];
    $paymentAmount = filter_var($_POST['payment_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Check if loan exists and belongs to the user
    $stmt = $pdo->prepare("SELECT * FROM loans WHERE loan_id = ? AND user_id = ?");
    $stmt->execute([$loanId, $userId]);
    $loan = $stmt->fetch();

    if (!$loan) {
        $error = "Loan not found.";
    } elseif ($paymentAmount <= 0) {
        $error = "Invalid payment amount.";
    } else {
        // The 'total_due' column represents the total amount due (principal + interest)
        $totalDue = $loan['total_due']; // Total amount due including principal and interest

        // Ensure the payment does not exceed the total loan balance
        if ($paymentAmount > $totalDue) {
            $error = "Payment amount exceeds the total loan balance.";
        } elseif ($balance < $paymentAmount) {
            $error = "Insufficient balance to make the payment.";
        } else {
            try {
                // Deduct payment from balance
                $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE user_id = ?");
                $stmt->execute([$paymentAmount, $userId]);

                // Calculate the remaining amount after payment
                $remainingAmount = $totalDue - $paymentAmount; // Subtract the paid amount from total due

                // Round the remaining amount to two decimal places
                $remainingAmount = round($remainingAmount, 2);

                if ($remainingAmount > 0) {
                    // Partial payment, update the loan's total_due
                    $stmt = $pdo->prepare("UPDATE loans SET total_due = ? WHERE loan_id = ?");
                    $stmt->execute([$remainingAmount, $loanId]);

                    // Insert loan payment transaction into transactions table
                    $stmt = $pdo->prepare("INSERT INTO transactions (account_id, type, amount, description, related_account_id, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$account['account_id'], 'loan_payment', $paymentAmount, 'Partial Loan Payment', null, date('Y-m-d H:i:s')]);

                    // Set success message in session
                    $_SESSION['success_message'] = "Partial loan payment has been made. Remaining loan balance: $" . number_format($remainingAmount, 2);
                } else {
                    // Loan fully paid, update loan status
                    $stmt = $pdo->prepare("UPDATE loans SET is_paid = 1, total_due = 0 WHERE loan_id = ?");
                    $stmt->execute([$loanId]);

                    // Insert full loan payment transaction into transactions table
                    $stmt = $pdo->prepare("INSERT INTO transactions (account_id, type, amount, description, related_account_id, created_at) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$account['account_id'], 'loan_payment', $paymentAmount, 'Full Loan Payment', null, date('Y-m-d H:i:s')]);

                    // Set success message in session
                    $_SESSION['success_message'] = "Loan has been fully paid.";

                    // If the total_due is now 0, remove the loan from the database
                    if ($remainingAmount == 0) {
                        // Delete the loan that has been fully paid
                        $stmt = $pdo->prepare("DELETE FROM loans WHERE loan_id = ?");
                        $stmt->execute([$loanId]);

                        // Set success message for deletion
                        $_SESSION['success_message'] = "Loan has been fully paid and removed.";
                    }
                }

                // Redirect to prevent resubmission of form data
                header("Location: loan-payment.php");
                exit();
            } catch (Exception $e) {
                $error = "Failed to process payment: " . $e->getMessage();
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
    <title>SecureBank - Loan Payment</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        // Display the success alert if it exists in the session and then reload the page
        <?php if (isset($_SESSION['success_message'])): ?>
            window.onload = function() {
                alert("<?= $_SESSION['success_message'] ?>");
                <?php unset($_SESSION['success_message']); ?> // Remove the success message from session
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
            <h2>Make a Payment</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="loan_id">Loan ID</label>
                    <input type="text" name="loan_id" id="loan_id" required>
                </div>

                <div class="form-group">
                    <label for="payment_amount">Payment Amount ($)</label>
                    <input type="number" name="payment_amount" id="payment_amount" min="0.01" step="0.01" required>
                </div>

                <button type="submit" class="btn">Pay Loan</button>
            </form>

            <h2>Your Active Loans</h2>
            <?php if (empty($loans)): ?>
                <p>You have no outstanding loans.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Amount Due (Including Interest)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><?= $loan['loan_id'] ?></td>
                                <td>$<?= number_format($loan['total_due'], 2) ?></td> <!-- Display total amount due including interest -->
                                <td><?= $loan['is_paid'] ? 'Paid' : 'Pending' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
