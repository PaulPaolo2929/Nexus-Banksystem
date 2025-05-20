<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

// Handle message deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $_SESSION['success'] = "Message deleted successfully.";
    } catch (PDOException $e) {
        error_log("Error deleting message: " . $e->getMessage());
        $_SESSION['error'] = "Failed to delete message.";
    }
    header("Location: manage-messages.php");
    exit();
}

// Pagination setup
$perPage = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
// Count total messages
$totalCount = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

try {
    // Fetch all contact messages ordered by newest first (paginated)
    $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :perPage OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching messages: " . $e->getMessage());
    die("Error fetching messages. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Contact Messages</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <style>
        .message-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .message-table th, .message-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .message-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .message-table tr:hover {
            background-color: #f5f5f5;
        }
        .status-new {
            color: #dc3545;
            font-weight: bold;
        }
        .status-read {
            color: #28a745;
        }
        .status-replied {
            color: #007bff;
        }
        .message-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .view-message {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }
        .view-message:hover {
            text-decoration: underline;
        }
        .delete-message {
            color: #dc3545;
            text-decoration: none;
            cursor: pointer;
        }
        .delete-message:hover {
            text-decoration: underline;
        }
        .no-messages {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .pagination { text-align: center; margin: 20px 0; }
        .pagination a { display: inline-block; margin: 0 4px; padding: 6px 12px; color: #007bff; background: #fff; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; transition: background 0.2s, color 0.2s; }
        .pagination a.btn-primary, .pagination a.active { background: #007bff; color: #fff; border-color: #007bff; pointer-events: none; }
        .pagination a:hover:not(.btn-primary):not(.active) { background: #f0f0f0; }
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
                <a href="manage-loans.php" class="btn">Manage Loans</a>
                <a href="manage-investments.php" class="btn">Manage Investments</a>
                <a href="track-investments.php" class="btn">Users Investments</a>
                <a href="role.php" class="btn">Roles</a>
                <a href="recent_transactions.php" class="btn">Transactions</a>
                <a href="loan-history.php" class="btn">Loan History</a>
                <a href="login-records.php" class="btn">Login Records</a>
                <a href="manage-messages.php" class="btn active">Contact Messages</a>
            </nav>

            <div class="logout-cont">
                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </aside>

        <main class="container">
            <header>
                <h1>Contact Messages</h1>
                <button class="hamburger">&#9776;</button>
            </header>

            <div class="content">
                <h2>Contact Form Messages</h2>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (!empty($messages)): ?>
                    <table class="message-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $message): ?>
                                <tr>
                                    <td><?php echo date("Y-m-d H:i", strtotime($message['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($message['name']); ?></td>
                                    <td><?php echo htmlspecialchars($message['email']); ?></td>
                                    <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                    <td class="message-content"><?php echo htmlspecialchars($message['message']); ?></td>
                                    <td>
                                        <span class="status-<?php echo $message['status']; ?>">
                                            <?php echo ucfirst($message['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view-message.php?id=<?php echo $message['id']; ?>" class="view-message">View</a>
                                        <a href="manage-messages.php?delete=<?php echo $message['id']; ?>" 
                                           class="delete-message" 
                                           onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-messages">
                        <p>No contact messages found.</p>
                    </div>
                <?php endif; ?>
                <!-- Pagination Controls -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'btn-primary active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html> 