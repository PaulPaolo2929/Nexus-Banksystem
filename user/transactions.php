<?php
// Show all errors (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];

// Get account ID
$stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();

if (!$account) {
    echo "<p>No account found for this user.</p>";
    exit();
}

$accountId = $account['account_id'];

// Build the query based on filters
$where = ["t.account_id = ?"];
$params = [$accountId];

// Apply search filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where[] = "(t.description LIKE ? OR t.transaction_id LIKE ?)";
    $params[] = "%{$_GET['search']}%";
    $params[] = "%{$_GET['search']}%";
}

// Apply type filter
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $where[] = "t.type = ?";
    $params[] = $_GET['type'];
}

// Apply date filter
if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
    $where[] = "DATE(t.created_at) >= ?";
    $params[] = $_GET['date_from'];
}

if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
    $where[] = "DATE(t.created_at) <= ?";
    $params[] = $_GET['date_to'];
}

// Get total number of transactions for pagination
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM transactions t
    LEFT JOIN accounts a ON t.related_account_id = a.account_id
    WHERE " . implode(" AND ", $where)
);
$stmt->execute($params);
$totalTransactions = $stmt->fetchColumn();

// Calculate pagination
$transactionsPerPage = 10;
$totalPages = ceil($totalTransactions / $transactionsPerPage);
$currentPage = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$offset = ($currentPage - 1) * $transactionsPerPage;

// Get transactions with pagination and filters
$stmt = $pdo->prepare("
    SELECT t.*, a.account_number as related_account_number
    FROM transactions t
    LEFT JOIN accounts a ON t.related_account_id = a.account_id
    WHERE " . implode(" AND ", $where) . "
    ORDER BY t.created_at DESC
    LIMIT ? OFFSET ?
");

$params[] = $transactionsPerPage;
$params[] = $offset;
$stmt->execute($params);
$transactions = $stmt->fetchAll();

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
$profilePic = $user['profile_picture'] ? '../uploads/' . $user['profile_picture'] : '../assets/images/default-avatar.png';
// Fetch user's profile information

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Transactions</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/transaction.css">

    <!-- Apexchart js API -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- NAVIGATION EFFECTS -->
    <script src="../assets/js/navhover.js"></script>
    <script src="../assets/js/sidebar.js"></script>

    <style>
    
    .transaction-distribution-chart, .weekly-activity-chart, .balance-over-time-chart {
      margin-top: 2rem;
      background: #fff;
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    /* Search and Filter Styles */
    .search-filter-container {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .search-filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
    }

    .search-box input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .filter-box {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-box select,
    .filter-box input[type="date"] {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        min-width: 150px;
    }

    .filter-button {
        padding: 10px 20px;
        background-color: #706EFF;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .filter-button:hover {
        background-color: #5856cc;
    }

    .reset-button {
        padding: 10px 20px;
        background-color: #6c757d;
        color: white;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .reset-button:hover {
        background-color: #5a6268;
    }

    .btn-download {
        display: inline-block;
        padding: 6px 12px;
        background: white;
        color: #706EFF;
        border: 1px solid #706EFF;
        border-radius: 20px;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
    }

    .btn-download:hover {
        background-color: #706EFF;
        color: white;
    }

    /* Existing Pagination Styles */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
        gap: 10px;
    }

    .page-link {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #706EFF;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background-color: #706EFF;
        color: white;
        border-color: #706EFF;
    }

    .page-current {
        padding: 8px 12px;
        background-color: #706EFF;
        color: white;
        border-radius: 4px;
        border: 1px solid #706EFF;
    }

    .page-ellipsis {
        color: #666;
        padding: 8px 12px;
    }

    .date-range-inputs {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .date-input-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .date-input-group label {
        font-size: 14px;
        color: #666;
        white-space: nowrap;
    }

    .filter-box select,
    .filter-box input[type="date"] {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        min-width: 150px;
        background-color: white;
    }

    .filter-box input[type="date"] {
        min-width: 130px;
        cursor: pointer;
    }

    .filter-box input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
    }

    /* Add a subtle separator between filter elements */
    .filter-box select {
        border-right: 2px solid #eee;
        padding-right: 15px;
        margin-right: 15px;
    }
 </style>
 

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
                        data-hover="../assets/images/hover-dashboard.png"
                        > 
                        Dashboard
                    </a>

                    <a href="deposit.php" class="btn">
                        <img 
                        src="../assets/images/inactive-deposit.png" 
                        alt="deposit-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-deposit.png"
                        data-hover="../assets/images/hover-deposit.png"
                        > 
                        Deposit
                    </a>

                    <a href="withdraw.php" class="btn">
                        <img 
                        src="../assets/images/inactive-withdraw.png" 
                        alt="withdraw-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-withdraw.png"
                        data-hover="../assets/images/hover-withdraw.png"
                        > 
                        Withdraw
                    </a>

                    <a href="transfer.php" class="btn">
                        <img 
                        src="../assets/images/inactive-transfer.png" 
                        alt="transfer-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transfer.png"
                        data-hover="../assets/images/hover-transfer.png"
                        > 
                        Transfer
                    </a>

                    <a href="transactions.php" class="btn dash-text">
                        <img 
                        src="../assets/images/hover-transaction.png" 
                        alt="transactions-logo" 
                        class="nav-icon"
                        data-default="../assets/images/hover-transaction.png"
                        data-hover="../assets/images/hover-transaction.png"
                        > 
                        Transactions
                    </a>

                    <a href="investment.php" class="btn">
                        <img 
                        src="../assets/images/inactive-investment.png" 
                        alt="investment-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-investment.png"
                        data-hover="../assets/images/hover-investment.png"
                        > 
                        Investment
                    </a>

                    <a href="loan.php" class="btn">
                        <img 
                        src="../assets/images/inactive-loans.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-loans.png"
                        data-hover="../assets/images/hover-loans.png"
                        > 
                        Loans
                    </a>

                    <a href="profile.php" class="btn">
                        <img 
                        src="../assets/images/inactive-profile.png" 
                        alt="loans-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-profile.png"
                        data-hover="../assets/images/inactive-profile"
                        > 
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
                    <h1>Transactions</h1>
                    <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
                </header>
                

                <div class="content">

                <div class="weekly-activity-chart">
                        <h2>Weekly Activity </h2>
                        <div id="Transchart"></div>
                    </div>

                    <h2>All Transactions</h2>
                    <div class="transactions-container">
                        <!-- Search and Filter Form -->
                        <div class="search-filter-container">
                            <form method="GET" class="search-filter-form">
                                <div class="search-box">
                                    <input type="text" name="search" 
                                           placeholder="Search by description or transaction ID"
                                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                                </div>
                                
                                <div class="filter-box">
                                    <select name="type">
                                        <option value="">All Types</option>
                                        <option value="deposit" <?= ($_GET['type'] ?? '') === 'deposit' ? 'selected' : '' ?>>Deposit</option>
                                        <option value="withdrawal" <?= ($_GET['type'] ?? '') === 'withdrawal' ? 'selected' : '' ?>>Withdraw</option>
                                        <option value="transfer_in" <?= ($_GET['type'] ?? '') === 'transfer_in' ? 'selected' : '' ?>>Transfer In</option>
                                        <option value="transfer_out" <?= ($_GET['type'] ?? '') === 'transfer_out' ? 'selected' : '' ?>>Transfer Out</option>
                                    </select>

                                    <div class="date-range-inputs">
                                        <div class="date-input-group">
                                            <label for="date_from">From:</label>
                                            <input type="date" 
                                                   id="date_from"
                                                   name="date_from" 
                                                   value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>"
                                                   aria-label="Start date">
                                        </div>
                                        
                                        <div class="date-input-group">
                                            <label for="date_to">To:</label>
                                            <input type="date" 
                                                   id="date_to"
                                                   name="date_to" 
                                                   value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>"
                                                   aria-label="End date">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="filter-button">Apply Filters</button>
                                <?php if (!empty($_GET)): ?>
                                    <a href="transactions.php" class="reset-button">Reset Filters</a>
                                <?php endif; ?>
                            </form>
                        </div>

                        <div class="transactions-table-wrapper">
                            <table class="transactions-table">
                            <thead>
                                <tr>
                                <th></th>
                                <th>Date</th>
                                <th>Transaction ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Related Account</th>
                                <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $txn): ?>
                                <tr>
                                <!-- arrow icon -->
                                <td class="icon" style="width: 32px; text-align: center;">
                                    <?php if (in_array($txn['type'], ['deposit','transfer_in'])): ?>
                                        <img src="../assets/images/Trans-up.png" alt="arrow Up" style="width: 30px; height: 30px; display: inline-block;">
                                    <?php else: ?>
                                        <img src="../assets/images/Trans-down.png" alt="arrow down" style="width: 30px; height: 30px; display: inline-block;">
                                    <?php endif; ?>
                                </td>

                                <td><?= date('j M, g:i A', strtotime($txn['created_at'])) ?></td>
                                <td><?= htmlspecialchars($txn['transaction_id']) ?></td>
                                <td><?= ucfirst($txn['type']) ?></td>
                                <td class="amount <?= in_array($txn['type'],['deposit','transfer_in'])? 'positive':'negative' ?>">
                                    <?= (in_array($txn['type'],['deposit','transfer_in'])? '+':'−') .
                                        '$'.number_format($txn['amount'],2) ?>
                                </td>
                                <td><?= $txn['related_account_number'] ?: 'N/A' ?></td>
                                <td>
                                    <a 
                                        href="generate_receipt.php?transaction_id=<?= urlencode($txn['transaction_id']) ?>" 
                                        class="btn-download"
                                        title="Download Receipt for <?= htmlspecialchars($txn['transaction_id']) ?>"
                                    >
                                        Download
                                    </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
                    </div>
                </div>
            </main>

    </div>
    

    <script>
          // Weekly Activiy Column Chart
 document.addEventListener("DOMContentLoaded", function () {
    fetch('get_weekly_activity.php')
        .then(response => response.json())
        .then(data => {
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            const deposits = Array(7).fill(0);
            const withdrawals = Array(7).fill(0);
            const transferIn = Array(7).fill(0);
            const transferOut = Array(7).fill(0);
            const loanPayments = Array(7).fill(0);

            data.forEach(entry => {
                const index = days.indexOf(entry.day);
                if (index !== -1) {
                    deposits[index] = parseFloat(entry.total_deposit);
                    withdrawals[index] = parseFloat(entry.total_withdraw);
                    transferIn[index] = parseFloat(entry.total_transfer_in);
                    transferOut[index] = parseFloat(entry.total_transfer_out);
                    loanPayments[index] = parseFloat(entry.total_loanpayment);
                }
            });

            const options = {
                chart: {
                    type: 'bar',
                    height: 400
                },
                title: {
                    text: ' '
                },
                xaxis: {
                    categories: days
                },
                yaxis: {
                    title: {
                        text: 'Amount ($)'
                    }
                },
                series: [
                    {
                        name: 'Deposits',
                        data: deposits
                    },
                    {
                        name: 'Withdrawals',
                        data: withdrawals
                    },
                    {
                        name: 'Transfers',
                        data: transferIn
                    },
                    {
                        name: 'Loan Payments',
                        data: loanPayments
                    },
                    {
                        name: 'Transfers Out',
                        data: transferOut
                    }
                ],
                colors: ['#706EFF', '#343C6A', '#00B8D9', '#FF6F61', '#FF9800'],
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    offsetX: 40
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return "$" + val.toFixed(2);
                        }
                    }
                },
                grid: {
                    borderColor: '#e0e0e0',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: -10,
                            offsetY: 0
                        }
                    }
                }]



            };

            const chart = new ApexCharts(document.querySelector("#Transchart"), options);
            chart.render();
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
});
    </script>
</body>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set session timeout to 10 minutes
        const inactivityTime = 600000;
        let inactivityTimer;

        const resetInactivityTimer = () => {
            // Clear existing timer
            if (inactivityTimer) clearTimeout(inactivityTimer);

            // Set timeout
            inactivityTimer = setTimeout(() => {
                window.location.href = '../logout.php?timeout=1';
            }, inactivityTime);
        };

        // Reset timer on user activity
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        events.forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        // Initial timer start
        resetInactivityTimer();
    });
    </script>
</html>
