<?php
require_once '../includes/db.php';
require_once '../includes/mailer.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

// Handle message deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $_SESSION['success'] = "Message deleted successfully.";
        header("Location: manage-messages.php");
        exit();
    } catch (PDOException $e) {
        error_log("Error deleting message: " . $e->getMessage());
        $_SESSION['error'] = "Failed to delete message.";
    }
}

// Check if message ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage-messages.php');
    exit();
}

$message_id = $_GET['id'];
$reply_sent = false;
$reply_error = null;

try {
    // Fetch the message
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $message = $stmt->fetch();

    if (!$message) {
        header('Location: manage-messages.php');
        exit();
    }

    // Update status to 'read' if it's new
    if ($message['status'] === 'new') {
        $updateStmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
        $updateStmt->execute([$message_id]);
    }

    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
        $newStatus = $_POST['status'];
        if (in_array($newStatus, ['read', 'replied'])) {
            $updateStmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $message_id]);
            header("Location: view-message.php?id=" . $message_id);
            exit();
        }
    }

    // Handle reply submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
        $reply_message = $_POST['reply_message'];
        
        // Initialize PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nexusbanksystem@gmail.com';
            $mail->Password   = 'ntnl ttfo uayi vsxd';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('nexusbanksystem@gmail.com', 'Nexus Bank');
            $mail->addAddress($message['email'], $message['name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Re: ' . $message['subject'];
            $mail->Body    = nl2br($reply_message);
            $mail->AltBody = strip_tags($reply_message);

            $mail->send();
            
            // Update message status to replied
            $updateStmt = $pdo->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
            $updateStmt->execute([$message_id]);
            
            $reply_sent = true;
        } catch (Exception $e) {
            $reply_error = "Failed to send reply: " . $mail->ErrorInfo;
            error_log("Mail Error: " . $mail->ErrorInfo);
        }
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    die("An error occurred. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Message</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <style>
        .message-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .message-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .message-header h2 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .message-meta {
            color: #666;
            font-size: 0.9em;
        }
        .message-content {
            line-height: 1.6;
            margin: 20px 0;
        }
        .message-actions {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .status-form {
            display: inline-block;
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            display: inline-block;
            width: auto;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
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
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 600px;
            position: relative;
        }
        .close-modal {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
        }
        .close-modal:hover {
            color: #000;
        }
        .reply-form {
            margin-top: 20px;
        }
        .reply-form textarea {
            width: 100%;
            min-height: 200px;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        .modal-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .modal-header h3 {
            margin: 0;
            color: #333;
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
                <h1>View Message</h1>
                <button class="hamburger">&#9776;</button>
            </header>

            <div class="content">
                <a href="manage-messages.php" class="back-link">&larr; Back to Messages</a>

                <?php if ($reply_sent): ?>
                    <div class="alert alert-success">Reply sent successfully!</div>
                <?php endif; ?>

                <?php if ($reply_error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($reply_error); ?></div>
                <?php endif; ?>

                <div class="message-container">
                    <div class="message-header">
                        <h2><?php echo htmlspecialchars($message['subject']); ?></h2>
                        <div class="message-meta">
                            <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?> (<?php echo htmlspecialchars($message['email']); ?>)</p>
                            <p><strong>Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($message['created_at'])); ?></p>
                            <p><strong>Status:</strong> <?php echo ucfirst($message['status']); ?></p>
                        </div>
                    </div>

                    <div class="message-content">
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>

                    <div class="message-actions">
                        <form method="POST" class="status-form">
                            <input type="hidden" name="status" value="replied">
                            <button type="submit" class="btn btn-success">Mark as Replied</button>
                        </form>
                        <button onclick="openReplyModal()" class="btn btn-primary">Reply via Email</button>
                        <a href="view-message.php?delete=<?php echo $message['id']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Reply Modal -->
    <div id="replyModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeReplyModal()">&times;</span>
            <div class="modal-header">
                <h3>Reply to: <?php echo htmlspecialchars($message['name']); ?></h3>
                <p>Subject: Re: <?php echo htmlspecialchars($message['subject']); ?></p>
            </div>
            <form method="POST" class="reply-form">
                <textarea name="reply_message" placeholder="Type your reply here..." required></textarea>
                <button type="submit" class="btn btn-primary">Send Reply</button>
            </form>
        </div>
    </div>

    <script>
        function openReplyModal() {
            document.getElementById('replyModal').style.display = 'block';
        }

        function closeReplyModal() {
            document.getElementById('replyModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var modal = document.getElementById('replyModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 