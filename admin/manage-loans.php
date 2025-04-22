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
        $status = ($action == 'approve') ? 'accepted' : 'rejected';
        $approvedAt = ($status == 'accepted') ? date('Y-m-d H:i:s') : null;

        // Update loan status in the database
        if ($status === 'accepted') {
            // Get loan details
            $stmt = $pdo->prepare("SELECT amount, interest_rate FROM loans WHERE loan_id = ?");
            $stmt->execute([$loanId]);
            $loan = $stmt->fetch();

            if ($loan) {
                $amount = $loan['amount'];
                $interest = $loan['interest_rate'];

                // Calculate total due (principal + interest)
                $totalDue = $amount + ($amount * ($interest / 100));

                // Update loan with approved info
                $stmt = $pdo->prepare("UPDATE loans SET status = ?, approved_at = ?, total_due = ?, amount = ?, is_paid = 'pending' WHERE loan_id = ?");
                $stmt->execute([$status, $approvedAt, $totalDue, $amount, $loanId]);
            }
        } else {
            // Just reject loan
            $stmt = $pdo->prepare("UPDATE loans SET status = ?, approved_at = NULL WHERE loan_id = ?");
            $stmt->execute([$status, $loanId]);
        }

        // If the loan is approved, update the user's balance
        if ($status == 'accepted') {
            $stmt = $pdo->prepare("SELECT amount, user_id FROM loans WHERE loan_id = ?");
            $stmt->execute([$loanId]);
            $loan = $stmt->fetch();

            if ($loan) {
                $loanAmount = $loan['amount'];
                $userId = $loan['user_id'];

                // Update user balance
                $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE user_id = ?");
                $stmt->execute([$loanAmount, $userId]);
            }
        }

        header("Location: manage-loans.php");
        exit;
    } elseif ($action == 'delete') {
        $stmt = $pdo->prepare("DELETE FROM loans WHERE loan_id = ?");
        $stmt->execute([$loanId]);

        header("Location: manage-loans.php");
        exit;
    }
}

// Fetch all loans with user info
$loans = $pdo->query("
    SELECT l.*, u.full_name, u.email 
    FROM loans l
    JOIN users u ON l.user_id = u.user_id
    ORDER BY l.created_at DESC
")->fetchAll();
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
    </nav>

    <div class="content">
        <h2>All Loan Requests</h2>

        <?php if (empty($loans)): ?>
            <p>No loan applications found.</p>
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
                        <th>Status</th>
                        <th>Purpose</th>
                        <th>Requested</th>
                        <th>Approved</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td><?= $loan['loan_id'] ?></td>
                            <td><?= htmlspecialchars($loan['full_name']) ?></td>
                            <td><?= htmlspecialchars($loan['email']) ?></td>
                            <td>$<?= number_format($loan['amount'], 2) ?></td>
                            <td><?= $loan['interest_rate'] ?>%</td>
                            <td><?= $loan['term_months'] ?> months</td>
                            <td><?= ucfirst($loan['status']) ?></td>
                            <td><?= htmlspecialchars($loan['purpose']) ?></td>
                            <td><?= date('M d, Y', strtotime($loan['created_at'])) ?></td>
                            <td><?= $loan['approved_at'] ? date('M d, Y', strtotime($loan['approved_at'])) : '—' ?></td>
                            <td>
                                <?php if ($loan['status'] === 'pending'): ?>
                                    <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=approve" class="btn btn-approve" onclick="return confirm('Approve this loan?')">Approve</a>
                                    <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=reject" class="btn btn-reject" onclick="return confirm('Reject this loan?')">Reject</a>
                                <?php endif; ?>
                                <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=delete" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this loan?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
