<?php 
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/notification.php';  // Include notification script

// Redirect if not admin
redirectIfNotAdmin();

// Handle loan approval, rejection, or deletion
if (isset($_GET['id']) && isset($_GET['action'])) {
    $loanId = $_GET['id'];
    $action = $_GET['action'];

    // Initialize the status and email variables
    $status = null;
    $subject = '';
    $messageHtml = '';
    $messagePlain = '';

    if ($action == 'approve' || $action == 'reject') {
        $status = ($action == 'approve') ? 'approved' : 'rejected';
        $approvedAt = ($status == 'approved') ? date('Y-m-d H:i:s') : null;

        // Fetch loan details
        $stmt = $pdo->prepare("SELECT amount, interest_rate, user_id FROM loans WHERE loan_id = ?");
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
                $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE user_id = ?");
                $stmt->execute([$amount, $loan['user_id']]);
            }

            // Fetch user's email for notification
            $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
            $stmt->execute([$loan['user_id']]);
            $user = $stmt->fetch();

            if ($user) {
                // Prepare the email content
                if ($status == 'approved') {
                    $subject = 'Your Loan Request Has Been Approved';
                    $messageHtml = "<p>Dear User,</p><p>Your loan request has been <strong>approved</strong>.</p><p>Amount: $".number_format($amount, 2)."<br>Total Due: $".number_format($totalDue, 2)."</p><p>Thank you for choosing us.</p>";
                    $messagePlain = "Dear User, Your loan request has been approved.\nAmount: $".number_format($amount, 2)."\nTotal Due: $".number_format($totalDue, 2)."\nThank you for choosing us.";
                } else {
                    $subject = 'Your Loan Request Has Been Rejected';
                    $messageHtml = "<p>Dear User,</p><p>Your loan request has been <strong>rejected</strong>.</p><p>We regret to inform you that we are unable to process your loan request at this time.</p><p>Thank you.</p>";
                    $messagePlain = "Dear User, Your loan request has been rejected.\nWe regret to inform you that we are unable to process your loan request at this time.\nThank you.";
                }

                // Send the notification email
                sendNotification($user['email'], $subject, $messageHtml, $messagePlain);
            }
        }
    } elseif ($action == 'delete') {
        // Delete loan
        $stmt = $pdo->prepare("DELETE FROM loans WHERE loan_id = ?");
        $stmt->execute([$loanId]);

        // Delete loan history
        $stmt = $pdo->prepare("DELETE FROM loan_history WHERE loan_id = ?");
        $stmt->execute([$loanId]);

        // Fetch user's email for notification
        $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
        $stmt->execute([$loan['user_id']]);
        $user = $stmt->fetch();

        if ($user) {
            // Send deletion notification email
            sendNotification($user['email'], 'Your Loan Has Been Deleted', 'Your loan request has been deleted from our system.', 'Your loan request has been deleted from our system.');
        }
    }

    header("Location: manage-loans.php");
    exit;
}

// Limit for displaying results
$perPage = 10;
$pendingPage = isset($_GET['pending_page']) && is_numeric($_GET['pending_page']) ? (int)$_GET['pending_page'] : 1;
$approvedPage = isset($_GET['approved_page']) && is_numeric($_GET['approved_page']) ? (int)$_GET['approved_page'] : 1;
$pendingOffset = ($pendingPage - 1) * $perPage;
$approvedOffset = ($approvedPage - 1) * $perPage;

// Count total pending and approved loans
$totalPending = $pdo->query("SELECT COUNT(*) FROM loans WHERE status = 'pending'")->fetchColumn();
$totalPendingPages = ceil($totalPending / $perPage);
$totalApproved = $pdo->query("SELECT COUNT(*) FROM loans WHERE status = 'approved' AND approved_at IS NOT NULL")->fetchColumn();
$totalApprovedPages = ceil($totalApproved / $perPage);

// Fetch pending loan requests
$pendingLoansStmt = $pdo->prepare("
    SELECT l.*, u.full_name, u.email 
    FROM loans l
    JOIN users u ON l.user_id = u.user_id
    WHERE l.status = 'pending'
    ORDER BY l.created_at DESC
    LIMIT :perPage OFFSET :offset
");
$pendingLoansStmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$pendingLoansStmt->bindValue(':offset', $pendingOffset, PDO::PARAM_INT);
$pendingLoansStmt->execute();
$pendingLoans = $pendingLoansStmt->fetchAll();

// Fetch recent approved loans
$approvedLoansStmt = $pdo->prepare("
    SELECT l.*, u.full_name, u.email 
    FROM loans l
    JOIN users u ON l.user_id = u.user_id
    WHERE l.status = 'approved' AND l.approved_at IS NOT NULL
    ORDER BY l.approved_at DESC
    LIMIT :perPage OFFSET :offset
");
$approvedLoansStmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
$approvedLoansStmt->bindValue(':offset', $approvedOffset, PDO::PARAM_INT);
$approvedLoansStmt->execute();
$approvedLoans = $approvedLoansStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Loans - Nexus Bank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-loans.css">

       <script src="../assets/js/sidebar.js"></script>
             
</head>
<body>

 <div class="wrapper">
            <aside class="sidebar">
                        
                            <div class="Logos-cont">
                                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
                            </div>

                            <nav class="dashboard-nav">
                                <a href="dashboard.php" class="active btn ">Dashboard</a>
                                <a href="manage-users.php" class="btn ">Manage Users</a>
                                <a href="manage-loans.php" class="btn dash-text">Manage Loans</a>
                                <a href="manage-investments.php" class="btn">Manage Investments</a>
                                <a href="track-investments.php" class="btn">Users Investments</a>
                                <a href="role.php" class="btn">Roles</a>
                                <a href="recent_transactions.php" class="btn">Transactions</a>
                                <a href="loan-history.php" class="btn">Loan History</a>
                                <a href="login-records.php" class="btn">Login Records</a>
                                <a href="manage-messages.php" class="btn">Contact Messages</a>
                            </nav>

                             <div class="logout-cont">
                                <a href="../logout.php" class="logout">Logout</a>
                            </div>
                </aside>
                            
                    <main class="container">
                    <header>
                    <h1>Loan Management</h1>
                    <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
                    </header>

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
                                        <td data-label="Loan ID"><?= $loan['loan_id'] ?></td>
                                        <td data-label="User"><?= htmlspecialchars($loan['full_name']) ?></td>
                                        <td data-label="Email"><?= htmlspecialchars($loan['email']) ?></td>
                                        <td data-label="Amount">$<?= number_format($loan['amount'], 2) ?></td>
                                        <td data-label="Interest"><?= $loan['interest_rate'] ?>%</td>
                                        <td data-label="Term"><?= $loan['term_months'] ?> months</td>
                                        <td data-label="Purpose"><?= htmlspecialchars($loan['purpose']) ?></td>
                                        <td data-label="Requested"><?= date('M d, Y', strtotime($loan['created_at'])) ?></td>
                                        <td data-label="Actions">
                                            <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=approve" class="btn3 btn-approve" onclick="return confirm('Approve this loan?')">Approve</a>
                                            <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=reject" class="btn3 btn-reject" onclick="return confirm('Reject this loan?')">Reject</a>
                                            <a href="manage-loans.php?id=<?= $loan['loan_id'] ?>&action=delete" class="btn3 btn-delete" onclick="return confirm('Are you sure you want to delete this loan?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <!-- Pagination Controls for Pending Loans -->
                    <?php if ($totalPendingPages > 1): ?>
                    <style>
                    .pagination { text-align: center; margin: 20px 0; }
                    .pagination a { display: inline-block; margin: 0 4px; padding: 6px 12px; color: #007bff; background: #fff; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; transition: background 0.2s, color 0.2s; }
                    .pagination a.btn-primary, .pagination a.active { background: #007bff; color: #fff; border-color: #007bff; pointer-events: none; }
                    .pagination a:hover:not(.btn-primary):not(.active) { background: #f0f0f0; }
                    </style>
                    <div class="pagination">
                        <?php if ($pendingPage > 1): ?>
                            <a href="?pending_page=<?= $pendingPage - 1 ?>">&laquo; Prev</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPendingPages; $i++): ?>
                            <a href="?pending_page=<?= $i ?>" class="<?= $i == $pendingPage ? 'btn-primary active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($pendingPage < $totalPendingPages): ?>
                            <a href="?pending_page=<?= $pendingPage + 1 ?>">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
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
                                        <td data-label="Loan ID"><?= $loan['loan_id'] ?></td>
                                        <td data-label="User"><?= htmlspecialchars($loan['full_name']) ?></td>
                                        <td data-label="Email"><?= htmlspecialchars($loan['email']) ?></td>
                                        <td data-label="Amount">$<?= number_format($loan['amount'], 2) ?></td>
                                        <td data-label="Interest"><?= $loan['interest_rate'] ?>%</td>
                                        <td data-label="Term"><?= $loan['term_months'] ?> months</td>
                                        <td data-label="Total Due">$<?= number_format($loan['total_due'], 2) ?></td>
                                        <td data-label="Purpose"><?= htmlspecialchars($loan['purpose']) ?></td>
                                        <td data-label="Approved On"><?= date('M d, Y', strtotime($loan['approved_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <!-- Pagination Controls for Approved Loans -->
                    <?php if ($totalApprovedPages > 1): ?>
                    <div class="pagination">
                        <?php if ($approvedPage > 1): ?>
                            <a href="?approved_page=<?= $approvedPage - 1 ?>">&laquo; Prev</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalApprovedPages; $i++): ?>
                            <a href="?approved_page=<?= $i ?>" class="<?= $i == $approvedPage ? 'btn-primary active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($approvedPage < $totalApprovedPages): ?>
                            <a href="?approved_page=<?= $approvedPage + 1 ?>">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    </div>
        </main>
</div>
</body>
</html>
