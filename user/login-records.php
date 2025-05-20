<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Include database connection
require_once '../includes/db.php';

// Fetch login records along with user info
$sql = "
    SELECT lr.id, lr.ip_address, lr.user_agent, lr.status, lr.created_at,
           u.full_name, u.email
    FROM login_records lr
    JOIN users u ON u.user_id = lr.user_id
    WHERE lr.user_id = :user_id
    ORDER BY lr.created_at DESC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $login_records = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching login records: " . $e->getMessage());
    $login_records = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Login History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
            margin: 0;
        }

        .navbar {
            background-color: #007bff;
            padding: 10px 20px;
            color: white;
            display: flex;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 95%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            white-space: nowrap;
        }

        thead tr {
            background-color: #007bff;
            color: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
            word-break: break-word;
        }

        td.user-agent-cell {
            max-width: 300px;
            white-space: normal;
            overflow-wrap: break-word;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .status-success {
            color: green;
            font-weight: bold;
        }

        .status-failed {
            color: red;
            font-weight: bold;
        }

        @media screen and (max-width: 600px) {
            th, td {
                font-size: 12px;
                padding: 6px;
            }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="login-history.php">Login History</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Your Login History</h1>

        <table>
            <thead>
                <tr>
                    <th>Login ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Status</th>
                    <th>Login Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($login_records)): ?>
                    <?php foreach ($login_records as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['id']) ?></td>
                            <td><?= htmlspecialchars($record['full_name']) ?></td>
                            <td><?= htmlspecialchars($record['email']) ?></td>
                            <td><?= htmlspecialchars($record['ip_address']) ?></td>
                            <td class="user-agent-cell"><?= htmlspecialchars($record['user_agent']) ?></td>
                            <td class="status-<?= htmlspecialchars($record['status']) ?>">
                                <?= ucfirst(htmlspecialchars($record['status'])) ?>
                            </td>
                            <td><?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($record['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">No login records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
