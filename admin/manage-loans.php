<?php 
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Redirect if not admin
redirectIfNotAdmin();

// Handle loan approval, rejection, or deletion
if (isset($_GET['id']) && isset($_GET['action'])) {
    $loanId = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve' || $action == 'reject') {
        $status = ($action == 'approve') ? 'approved' : 'rejected';
        $approvedAt = ($status == 'approved') ? date('Y-m-d H:i:s') : null;

        // Fetch loan details
        $stmt = $pdo->prepare("SELECT amount, interest_rate FROM loans WHERE loan_id = ?");
        $stmt->execute([$loanId]);
        $loan = $stmt->fetch();

        if ($loan) {
            $amount = $loan['amount'];
            $interest = $loan['interest_rate'];
            $totalDue = $amount + ($amount * ($interest / 100));

            // Update loan status
            $stmt = $pdo->prepare("UPDATE loans SET status = ?, approved_at = ?, total_due = ?, amount = ?, is_paid = 'no' WHERE loan_id = ?");
            $stmt->execute([$status, $approvedAt, $totalDue, $amount, $loanId]);

            // Insert into loan_history to track status change
            $stmt = $pdo->prepare("INSERT INTO loan_history (loan_id, status, changed_at) VALUES (?, ?, ?)");
            $stmt->execute([$loanId, $status, date('Y-m-d H:i:s')]);

            // If the loan is approved, update the user's account balance
            if ($status == 'approved') {
                $stmt = $pdo->prepare("SELECT user_id FROM loans WHERE loan_id = ?");
                $stmt->execute([$loanId]);
                $loan = $stmt->fetch();

                if ($loan) {
                    $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE user_id = ?");
                    $stmt->execute([$amount, $loan['user_id']]);
                }
            }
        }
    } elseif ($action == 'delete') {
        // Delete loan
        $stmt = $pdo->prepare("DELETE FROM loans WHERE loan_id = ?");
        $stmt->execute([$loanId]);

        // Delete loan history
        $stmt = $pdo->prepare("DELETE FROM loan_history WHERE loan_id = ?");
        $stmt->execute([$loanId]);
    }

    header("Location: manage-loans.php");
    exit;
}

// Limit for displaying results
$limit = 10;

// Fetch pending loan requests
$pendingLoansStmt = $pdo->prepare("
    SELECT l.*, u.full_name, u.email 
    FROM loans l
    JOIN users u ON l.user_id = u.user_id
    WHERE l.status = 'pending'
    ORDER BY l.created_at DESC
    LIMIT ?
");
$pendingLoansStmt->bindValue(1, $limit, PDO::PARAM_INT);
$pendingLoansStmt->execute();
$pendingLoans = $pendingLoansStmt->fetchAll();

// Fetch recent approved loans
$approvedLoansStmt = $pdo->prepare("
    SELECT l.*, u.full_name, u.email 
    FROM loans l
    JOIN users u ON l.user_id = u.user_id
    WHERE l.status = 'approved' AND l.approved_at IS NOT NULL
    ORDER BY l.approved_at DESC
    LIMIT ?
");
$approvedLoansStmt->bindValue(1, $limit, PDO::PARAM_INT);
$approvedLoansStmt->execute();
$approvedLoans = $approvedLoansStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Loans - SecureBank Admin</title>
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
        .btn {
            padding: 0.4rem 0.7rem;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            margin-right: 0.3rem;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-reject {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete {
            background-color: #ffc107;
            color: white;
        }
        .logout {
            float: right;
            color: red;
            font-weight: bold;
        }
        header h1 {
            display: inline-block;
        }
        hr {
            margin: 2rem 0;
            border: none;
            border-top: 2px solid #ccc;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>Loan Management</h1>
        <a href="../logout.php" class="logout">Logout</a>
    </header>

    <nav class="dashboard-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="manage-users.php">Manage Users</a>
        <a href="manage-loans.php" class="active">Manage Loans</a>
        <a href="role.php">Roles</a>
        <a href="recent_transactions.php">Transactions</a>
    </nav>

    <div class="content">

        <h2>🕒 Pending Loan Requests (Latest 10)</h2>

        <?php if (empty($pendingLoans)): ?>
            <p>No pending loan applications at the moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Interest</th>
                        <th>Term</th>
                        <th>Purpose</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingLoans as $loan): ?>
                        <tr>
                            <td><?= $loan['loan_id'] ?></td>
                            <td><?= htmlspecialchars($loan['full_name']) ?></td>
                            <td><?= htmlspecialchars($loan['email']) ?></td>
                            <td>$<?= number_format($loan['amount'], 2) ?></td>
                            <td><?= $loan['interest_rate'] ?>%</td>
                            <td><?= $loan['term_months'] ?> months</td>
                            <td><?= htmlspecialchars($loan['purpose']) ?></td>
                            <td><?= date('M d, Y', strtotime($loan['created_at'])) ?></td>
                            <td>
                                <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=approve" class="btn btn-approve" onclick="return confirm('Approve this loan?')">Approve</a>
                                <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=reject" class="btn btn-reject" onclick="return confirm('Reject this loan?')">Reject</a>
                                <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=delete" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this loan?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <hr>

        <h2>✅ Recently Approved Loans (Latest 10)</h2>

        <?php if (empty($approvedLoans)): ?>
            <p>No approved loans yet.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Loan ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Interest</th>
                        <th>Term</th>
                        <th>Total Due</th>
                        <th>Purpose</th>
                        <th>Approved On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approvedLoans as $loan): ?>
                        <tr>
                            <td><?= $loan['loan_id'] ?></td>
                            <td><?= htmlspecialchars($loan['full_name']) ?></td>
                            <td><?= htmlspecialchars($loan['email']) ?></td>
                            <td>$<?= number_format($loan['amount'], 2) ?></td>
                            <td><?= $loan['interest_rate'] ?>%</td>
                            <td><?= $loan['term_months'] ?> months</td>
                            <td>$<?= number_format($loan['total_due'], 2) ?></td>
                            <td><?= htmlspecialchars($loan['purpose']) ?></td>
                            <td><?= date('M d, Y', strtotime($loan['approved_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

<a href="loan-history.php" class="btn">📜 View Full Loan History</a>
</body>
</html>
