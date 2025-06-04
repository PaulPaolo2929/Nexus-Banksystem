<?php 
session_start();

// Include database connection
require_once '../includes/db.php';
require_once '../includes/session_manager.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$recordsPerPage = 10;
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($currentPage - 1) * $recordsPerPage;

// Get total number of login records for pagination
try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM login_records WHERE user_id = :user_id");
    $countStmt->execute(['user_id' => $user_id]);
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $recordsPerPage);
} catch (PDOException $e) {
    error_log("Error counting login records: " . $e->getMessage());
    $totalRecords = 0;
    $totalPages = 0;
}

// Fetch login records along with user info with pagination
$sql = "
    SELECT lr.id, lr.ip_address, lr.user_agent, lr.status, lr.created_at,
           u.full_name, u.email
    FROM login_records lr
    JOIN users u ON u.user_id = lr.user_id
    WHERE lr.user_id = :user_id
    ORDER BY lr.created_at DESC
    LIMIT :limit OFFSET :offset
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $login_records = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching login records: " . $e->getMessage());
    $login_records = [];
}

// Get user account information
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.*, a.account_number, a.balance 
    FROM users u 
    JOIN accounts a ON u.user_id = a.user_id 
    WHERE u.user_id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die('User account not found.');
}

// Check if the user has a profile picture
$stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
// After fetching the user from the database
$profilePic = (!empty($user['profile_picture']) && file_exists('../uploads/' . $user['profile_picture']))
    ? '../uploads/' . $user['profile_picture']
    : '../assets/images/default-avatars.png';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Your Login History</title>
    <link rel="stylesheet" href="../assets/css/login-rec.css" />
   <script src="../assets/js/sidebar.js"></script>

</head>
<body>
<div class="wrapper">
   <aside class="sidebar">
            <div class="Logos-cont">
                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
            </div>
            <hr>
            <div class="profile-container">
                <img src="<?= $profilePic ?>" alt="Profile Picture" class="img-fluid">
                <h5><?= htmlspecialchars($user['full_name']) ?></h5>
                <p><?= htmlspecialchars($user['account_number']) ?></p>
            </div>
            <hr>
            <nav>
                <a href="dashboard.php" class="btn">
                    <img 
                        src="../assets/images/inactive-dashboard.png" 
                        alt="dashboard-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-dashboard.png"
                        data-hover="../assets/images/hover-dashboard.png"> 
                    Dashboard
                </a>
                <a href="deposit.php" class="btn">
                    <img 
                        src="../assets/images/inactive-deposit.png" 
                        alt="deposit-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-deposit.png"
                        data-hover="../assets/images/hover-deposit.png"> 
                    Deposit
                </a>
                <a href="withdraw.php" class="btn">
                    <img 
                        src="../assets/images/inactive-withdraw.png" 
                        alt="withdraw-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-withdraw.png"
                        data-hover="../assets/images/hover-withdraw.png"> 
                    Withdraw
                </a>
                <a href="transfer.php" class="btn">
                    <img 
                        src="../assets/images/inactive-transfer.png" 
                        alt="transfer-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transfer.png"
                        data-hover="../assets/images/hover-transfer.png"> 
                    Transfer
                </a>
                <a href="transactions.php" class="btn">
                    <img 
                        src="../assets/images/inactive-transaction.png" 
                        alt="transactions-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transaction.png"
                        data-hover="../assets/images/hover-transaction.png"> 
                    Transactions
                </a>
                <a href="investment.php" class="btn">
                    <img 
                        src="../assets/images/inactive-investment.png" 
                        alt="investment-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-investment.png"
                        data-hover="../assets/images/hover-investment.png"> 
                    Investment
                </a>
                <a href="loan.php" class="btn dash-text">
                    <img 
                        src="../assets/images/hover-loans.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/hover-loans.png"
                        data-hover="../assets/images/hover-loans.png"> 
                    Loans
                </a>
                <a href="profile.php" class="btn">
                    <img 
                        src="../assets/images/inactive-profile.png" 
                        alt="profile-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-profile.png"
                        data-hover="../assets/images/hover-profile.png"> 
                    Settings
                </a>
            </nav>
            <hr>
            <div class="logout-cont">
                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </aside>

    <main class="container">
                <header>
                    <h1>Login Records</h1>
                    <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
                </header>

                <div class="content">
                    <div class="filter-container">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" name="status">
                            <option value="">All</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                            <option value="pending">Pending</option>
                        </select>

                        <label for="start-date-filter">Start Date:</label>
                        <input type="date" id="start-date-filter" name="start_date" />

                        <label for="end-date-filter">End Date:</label>
                        <input type="date" id="end-date-filter" name="end_date" />

                        <button id="apply-filter-btn">Filter</button>
                    </div>
    

                    <table class="login-records-table">
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
                        <tbody id="login-records-tbody">
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

                     <!-- Pagination Controls -->
                            <?php if ($totalPages > 1): ?>
                            <div class="pagination">
                                <?php if ($currentPage > 1): ?>
                                    <a href="?page=<?= $currentPage - 1 ?>" class="page-link">&laquo; Previous</a>
                                <?php endif; ?>

                                <?php
                                // Show up to 5 page numbers, centered around current page
                                $startPage = max(1, min($currentPage - 2, $totalPages - 4));
                                $endPage = min($totalPages, max($currentPage + 2, 5));
                                
                                if ($startPage > 1) {
                                    echo '<span class="page-ellipsis">...</span>';
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    if ($i == $currentPage) {
                                        echo "<span class=\"page-current\">$i</span>";
                                    } else {
                                        echo "<a href=\"?page=$i\" class=\"page-link\">$i</a>";
                                    }
                                }
                                
                                if ($endPage < $totalPages) {
                                    echo '<span class="page-ellipsis">...</span>';
                                }
                                ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <a href="?page=<?= $currentPage + 1 ?>" class="page-link">Next &raquo;</a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                </div>

                    <script>
                        document.getElementById('apply-filter-btn').addEventListener('click', function() {
                            const status = document.getElementById('status-filter').value;
                            const startDate = document.getElementById('start-date-filter').value;
                            const endDate = document.getElementById('end-date-filter').value;
                            let currentPage = 1;

                            function fetchData(page = 1) {
                                const params = new URLSearchParams();
                                if (status) params.append('status', status);
                                if (startDate) params.append('start_date', startDate);
                                if (endDate) params.append('end_date', endDate);
                                params.append('page', page);

                                fetch('login-records-filter.php?' + params.toString())
                                    .then(response => response.json())
                                    .then(data => {
                                        const tbody = document.getElementById('login-records-tbody');
                                        tbody.innerHTML = '';

                                        if (data.data && data.data.length > 0) {
                                            data.data.forEach(record => {
                                                const tr = document.createElement('tr');

                                                tr.innerHTML = `
                                                    <td>${record.id}</td>
                                                    <td>${record.full_name}</td>
                                                    <td>${record.email}</td>
                                                    <td>${record.ip_address}</td>
                                                    <td class="user-agent-cell" title="${record.user_agent}">${record.user_agent}</td>
                                                    <td class="status-${record.status}">${record.status.charAt(0).toUpperCase() + record.status.slice(1)}</td>
                                                    <td>${record.created_at}</td>
                                                `;

                                                tbody.appendChild(tr);
                                            });
                                        } else {
                                            const tr = document.createElement('tr');
                                            tr.innerHTML = '<td colspan="7" style="text-align: center;">No login records found.</td>';
                                            tbody.appendChild(tr);
                                        }

                                        // Update pagination controls
                                        updatePagination(data.currentPage, data.totalPages);
                                    })
                                    .catch(error => {
                                        console.error('Error fetching filtered data:', error);
                                    });
                            }

                            function updatePagination(currentPage, totalPages) {
                                const paginationContainer = document.getElementById('pagination-container');
                                if (totalPages <= 1) {
                                    paginationContainer.innerHTML = '';
                                    return;
                                }

                                let html = '';

                                if (currentPage > 1) {
                                    html += `<a href="#" class="page-link" data-page="${currentPage - 1}">&laquo; Previous</a>`;
                                }

                                const startPage = Math.max(1, Math.min(currentPage - 2, totalPages - 4));
                                const endPage = Math.min(totalPages, Math.max(currentPage + 2, 5));

                                if (startPage > 1) {
                                    html += '<span class="page-ellipsis">...</span>';
                                }

                                for (let i = startPage; i <= endPage; i++) {
                                    if (i === currentPage) {
                                        html += `<span class="page-current">${i}</span>`;
                                    } else {
                                        html += `<a href="#" class="page-link" data-page="${i}">${i}</a>`;
                                    }
                                }

                                if (endPage < totalPages) {
                                    html += '<span class="page-ellipsis">...</span>';
                                }

                                if (currentPage < totalPages) {
                                    html += `<a href="#" class="page-link" data-page="${currentPage + 1}">Next &raquo;</a>`;
                                }

                                paginationContainer.innerHTML = html;

                                // Add event listeners to pagination links
                                const pageLinks = paginationContainer.querySelectorAll('.page-link');
                                pageLinks.forEach(link => {
                                    link.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        const page = parseInt(this.getAttribute('data-page'));
                                        if (!isNaN(page)) {
                                            fetchData(page);
                                        }
                                    });
                                });
                            }

                            // Initial fetch
                            fetchData();

                            // Update filters on button click
                            document.getElementById('apply-filter-btn').addEventListener('click', function() {
                                fetchData(1);
                            });
                        });
                    </script>
    </main>
    </div>
    <script src="../assets/js/session.js"></script>
</body>
</html>
