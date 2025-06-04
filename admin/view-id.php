<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header('Location: manage-users.php');
    exit();
}

$userId = (int)$_GET['user_id'];

try {
    // Get user and ID verification details
    $stmt = $pdo->prepare("
        SELECT u.*, iv.* 
        FROM users u 
        LEFT JOIN id_verifications iv ON u.user_id = iv.user_id 
        WHERE u.user_id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: manage-users.php');
        exit();
    }

    // Handle ID verification status update - REMOVED
    /*
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_status'])) {
        $newStatus = $_POST['verification_status'];
        if (in_array($newStatus, ['verified', 'rejected'])) {
            $stmt = $pdo->prepare("
                UPDATE id_verifications 
                SET verification_status = ?, verified_at = CURRENT_TIMESTAMP 
                WHERE user_id = ?
            ");
            $stmt->execute([$newStatus, $userId]);
            
            // If ID is verified, also approve the user
            if ($newStatus === 'verified' && $user['status'] === 'pending') {
                $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE user_id = ?");
                $stmt->execute([$userId]);
                
                // Create account for the user
                $accountNumber = generateUniqueAccountNumber($pdo);
                $stmt = $pdo->prepare("INSERT INTO accounts (user_id, account_number, balance) VALUES (?, ?, 0)");
                $stmt->execute([$userId, $accountNumber]);
            }
            
            header("Location: view-id.php?user_id=$userId&success=1");
            exit();
        }
    }
    */
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    header('Location: manage-users.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View ID - Nexus Bank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <style>
        main.container {
            /* padding-top: 60px; /* Add padding to push content below header */ */
        }
        
        /* Add top margin to the first element after the header */
        main.container > *:not(header):first-child {
            margin-top: 80px; /* Adjust this value if needed */
        }

        .id-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 120px; /* Increase top margin to ensure content is below header */
        }
        .id-details {
            margin-bottom: 20px;
        }
        .id-details p {
            margin: 10px 0;
        }
        .id-document {
            max-width: 100%;
            margin: 20px 0;
        }
        .id-document img {
            max-width: 100%;
            height: auto;
        }
        .verification-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
                <a href="manage-users.php" class="btn active">Manage Users</a>
                <a href="manage-loans.php" class="btn">Manage Loans</a>
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
                <h1>View ID Verification</h1>
                <a href="manage-users.php" class="btn btn-secondary">Back to Users</a>
            </header>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    ID verification status updated successfully.
                </div>
            <?php endif; ?>

            <div class="id-container">
                <div class="id-details">
                    <h2>User Information</h2>
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
                    <p><strong>Occupation:</strong> <?= htmlspecialchars($user['occupation']) ?></p>
                </div>

                <div class="id-details">
                    <h2>ID Verification Details</h2>
                    <p><strong>ID Type:</strong> <?= ucwords(str_replace('_', ' ', $user['id_type'])) ?></p>
                    <p><strong>Status:</strong> <?= ucfirst($user['verification_status'] ?? 'pending') ?></p>
                    <p><strong>Uploaded On:</strong> <?= date('M j, Y g:i A', strtotime($user['created_at'])) ?></p>
                </div>

                <div class="id-document">
                    <h2>ID Document</h2>
                    <?php
                    $fileExtension = pathinfo($user['id_file_path'], PATHINFO_EXTENSION);
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])): ?>
                        <img src="../<?= htmlspecialchars($user['id_file_path']) ?>" alt="ID Document">
                    <?php else: ?>
                        <p>PDF Document: <a href="../<?= htmlspecialchars($user['id_file_path']) ?>" target="_blank">View PDF</a></p>
                    <?php endif; ?>
                </div>

                <?php if ($user['verification_status'] !== 'verified'): ?>
                    <!-- Removed ID verification action buttons -->
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html> 