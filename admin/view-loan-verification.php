<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

if (!isset($_GET['loan_id']) || !is_numeric($_GET['loan_id'])) {
    header('Location: manage-loans.php');
    exit();
}

$loanId = (int)$_GET['loan_id'];

try {
    // Get loan and user details
    $stmt = $pdo->prepare("
        SELECT l.*, u.full_name, u.email 
        FROM loans l 
        JOIN users u ON l.user_id = u.user_id 
        WHERE l.loan_id = ?
    ");
    $stmt->execute([$loanId]);
    $loan = $stmt->fetch();

    if (!$loan || (!$loan['id_selfie_file_path'] && !$loan['id_document_file_path'])) {
        // Redirect if loan not found or no files uploaded
        header('Location: manage-loans.php');
        exit();
    }

} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    header('Location: manage-loans.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Loan Verification - Nexus Bank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <style>
        .verification-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 80px;
        }
        .user-details,
        .document-section {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .user-details p,
        .document-section p {
            margin: 10px 0;
        }
        .document-section img,
        .document-section embed {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 10px;
        }
         .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="Logos-cont">
                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
            </div>
            <nav class="dashboard-nav">
                <a href="dashboard.php" class="btn">Dashboard</a>
                <a href="manage-users.php" class="btn">Manage Users</a>
                <a href="manage-loans.php" class="btn active">Manage Loans</a>
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
                <h1>Loan Verification</h1>
                 <a href="manage-loans.php" class="btn btn-secondary">Back to Loans</a>
            </header>

            <div class="verification-container">
                <div class="user-details">
                    <h2>User Information</h2>
                    <p><strong>Name:</strong> <?= htmlspecialchars($loan['full_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($loan['email']) ?></p>
                    <p><strong>Loan Amount:</strong> ₱<?= number_format($loan['amount'], 2) ?></p>
                    <p><strong>Purpose:</strong> <?= htmlspecialchars($loan['purpose']) ?></p>
                </div>

                <?php if ($loan['id_selfie_file_path']): ?>
                    <div class="document-section">
                        <h2>Selfie with ID</h2>
                        <?php $fileExtension = pathinfo($loan['id_selfie_file_path'], PATHINFO_EXTENSION);
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])): ?>
                             <img src="<?= htmlspecialchars($loan['id_selfie_file_path']) ?>" alt="Selfie with ID">
                        <?php else: ?>
                             <p>File: <a href="../<?= htmlspecialchars($loan['id_selfie_file_path']) ?>" target="_blank">View File</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($loan['id_document_file_path']): ?>
                    <div class="document-section">
                        <h2>ID Document</h2>
                         <?php $fileExtension = pathinfo($loan['id_document_file_path'], PATHINFO_EXTENSION);
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])): ?>
                             <img src="<?= htmlspecialchars($loan['id_document_file_path']) ?>" alt="ID Document">
                        <?php elseif ($fileExtension === 'pdf'): ?>
                            <p>PDF Document: <a href="<?= htmlspecialchars($loan['id_document_file_path']) ?>" target="_blank">View PDF</a></p>
                        <?php else: ?>
                             <p>File: <a href="<?= htmlspecialchars($loan['id_document_file_path']) ?>" target="_blank">View File</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html> 