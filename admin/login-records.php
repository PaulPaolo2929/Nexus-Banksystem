<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();

// Fetch all login records with user details
$stmt = $pdo->prepare("
    SELECT 
        lr.id,
        lr.user_id,
        u.full_name,
        u.email,
        lr.ip_address,
        lr.user_agent,
        lr.status,
        lr.created_at
    FROM login_records lr
    JOIN users u ON u.user_id = lr.user_id
    ORDER BY lr.created_at DESC
");
$stmt->execute();
$loginRecords = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Login Records - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 95%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
        }

        h1 {
            text-align: center;
            color: #333;
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
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
        }

        td:nth-child(6), td:nth-child(7) {
            max-width: 300px;
            word-break: break-word;
            white-space: normal;
        }

        .status-success {
            color: green;
            font-weight: bold;
        }

        .status-failed {
            color: red;
            font-weight: bold;
        }

        .back {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 15px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
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
    <div class="container">
        <h1>All Login Records</h1>

        <?php if (empty($loginRecords)): ?>
            <p>No login records found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Login Time</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loginRecords as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['id']) ?></td>
                            <td><?= htmlspecialchars($record['full_name']) ?></td>
                            <td><?= htmlspecialchars($record['email']) ?></td>
                            <td class="status-<?= htmlspecialchars($record['status']) ?>">
                                <?= ucfirst(htmlspecialchars($record['status'])) ?>
                            </td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($record['created_at']))) ?></td>
                            <td><?= htmlspecialchars($record['ip_address']) ?></td>
                            <td><?= htmlspecialchars($record['user_agent']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="dashboard.php" class="back">Back to Dashboard</a>
    </div>
</body>
</html>
