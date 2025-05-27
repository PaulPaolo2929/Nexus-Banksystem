<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/session_manager.php';

// Check session status
checkSessionStatus();

// Set the timeout duration (15 minutes in seconds)
$timeoutDuration = 900;  // 15 minutes

// Set the logout redirect URL
$logoutRedirectUrl = '../logout.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Check if session has expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutDuration) {
    // If session expired, log out user
    session_unset();
    session_destroy();
    header("Location: $logoutRedirectUrl"); // Redirect to logout page
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

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

// Get recent transactions
$stmt = $pdo->prepare("
    SELECT * FROM transactions 
    WHERE account_id = (SELECT account_id FROM accounts WHERE user_id = ?)
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([$userId]);
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
    <title>Nexus-Banksystem - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <script src="../assets/js/session.js"></script>

    <!-- Apexchart -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- NAVIGATION EFFECTS -->
    <script src="../assets/js/navhover.js"></script>
    <script src="../assets/js/sidebar.js"></script>
    <style>
    
       .transaction-distribution-chart, .weekly-activity-chart, .balance-over-time-chart {
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .btn-download {
            padding: 6px 12px;
            background: white;
            color: #706EFF;
            border: 1px solid #706EFF;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background-color: #706EFF;
            color: white;
        }

        .transactions-tabs {
            margin-bottom: 20px;
        }

        .transactions-tabs .tab {
            padding: 8px 16px;
            margin-right: 10px;
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 14px;
            transition: color 0.3s;
        }

        .transactions-tabs .tab:hover {
            color: #706EFF;
        }

        .transactions-tabs .tab.active {
            color: #706EFF;
            font-weight: bold;
            position: relative;
        }

        .transactions-tabs .tab.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #706EFF;
        }

        .transactions-table-wrapper {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
    
    <!--Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

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
                    <a href="dashboard.php" class="btn dash-text">
                        <img 
                        src="../assets/images/hover-dashboard.png" 
                        alt="dashboard-logo" 
                        class="nav-icon "
                        data-default="../assets/images/hover-dashboard.png"
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

                    <a href="transactions.php" class="btn">
                        <img 
                        src="../assets/images/inactive-transaction.png" 
                        alt="transactions-logo" 
                        class="nav-icon"
                        data-default="../assets/images/inactive-transaction.png"
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
                        <h1>Overview</h1>
                        <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
                    </header>
                    
                    
                    <div class="dashboard-content">
                        <div class="account-summary">
                             <h2>Welcome, <?= htmlspecialchars($user['full_name']) ?></h2>
                            <h2>Account Summary</h2>
                            <p>Account Number: <?= htmlspecialchars($user['account_number']) ?></p>
                            <p class="balance">Balance: ₱<?= number_format($user['balance'], 2) ?></p>
                        </div>
                        

                        <div class="quick-actions">
                            <h2>Quick Actions</h2>
                            <div class="action-buttons">      
                                <a href="deposit.php" class="btn1">Deposit Money</a>
                                <a href="withdraw.php" class="btn1">Withdraw Funds</a>
                                <a href="transfer.php" class="btn1">Transfer Funds</a>
                                <a href="loan.php" class="btn1">Apply for Loan</a>
                                <a href="loan-payment.php" class="btn1">Pay Loan</a>
                                <a href="profile.php" class="btn1">Profile</a>
                                <a href="login-records.php" class="btn1">Login Records</a>
                            </div>
                        </div>
                        
                        <h2>Recent Transactions</h2>
                        <div class="transactions-container">
                            
                                        <div class="transactions-tabs">
                                            <button class="tab active" data-type="all">All Transactions</button>
                                            <button class="tab" data-type="deposit">Deposit</button>
                                            <button class="tab" data-type="withdrawal">Withdraw</button>
                                            <button class="tab" data-type="transfer">Transfer</button>
                                        </div>

                                        <div class="transactions-table-wrapper">
                                            <table class="transactions-table">
                                            <thead>
                                                <tr>
                                                <th></th>
                                                <th>Transaction ID</th>
                                                <th>Description</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Receipt</th>
                                                </tr>
                                            </thead>
                                            <tbody id="transactionsTableBody">
                                                <?php foreach ($transactions as $txn): ?>
                                                <tr class="transaction-row" data-type="<?= htmlspecialchars($txn['type']) ?>">
                                                <!-- arrow icon -->
                                                <td class="icon" style="width: 32px; text-align: center;">
                                                    <?php if (in_array($txn['type'], ['deposit','transfer_in'])): ?>
                                                        <img src="../assets/images/Trans-up.png" alt="arrow Up" style="width: 30px; height: 30px; display: inline-block;">
                                                    <?php else: ?>
                                                        <img src="../assets/images/Trans-down.png" alt="arrow down" style="width: 30px; height: 30px; display: inline-block;">
                                                    <?php endif; ?>
                                                </td>

                                                <td><?= htmlspecialchars($txn['transaction_id']) ?></td>
                                                <td><?= htmlspecialchars($txn['description'] ?? '') ?></td>
                                                <td><?= ucfirst($txn['type']) ?></td>
                                                <td><?= date('j M, g:i A', strtotime($txn['created_at'])) ?></td>
                                                <td class="amount <?= in_array($txn['type'],['deposit','transfer_in'])? 'positive':'negative' ?>">
                                                    <?= (in_array($txn['type'],['deposit','transfer_in'])? '+':'−') .
                                                        '₱'.number_format($txn['amount'],2) ?>
                                                </td>
                                                <td>
                                                    <button onclick="window.open('generate_receipt.php?transaction_id=<?= htmlspecialchars($txn['transaction_id']) ?>', '_blank')" 
                                                            class="btn-download">Download</button>
                                                </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            </table>
                                        </div>
                                </div>


                        <div class="weekly-activity-chart">
                            <h2>Weekly Activity </h2>
                            <div id="chart"></div>
                        </div>

                        <div class="transaction-distribution-chart">
                            <h2>Expense Statistics</h2>
                            <div id="pieChart"></div>
                        </div>

                        <div class="balance-over-time-chart">
                            <h2>Balance Over Time</h2>
                            <div id="balancechart"></div>
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
                        text: 'Amount (₱)'
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
                        name: 'Transfers In',
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
                            return "₱" + val.toFixed(2);
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

            const chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
});



        // Transaction Type Distribution Pie Chart
        document.addEventListener("DOMContentLoaded", function () {
        fetch('get_transaction_distribution.php')
            .then(response => response.json())
            .then(data => {
                const labels = [];
                const values = [];

                data.forEach(entry => {
                    let label = '';
                    switch (entry.type) {
                        case 'deposit': label = 'Deposit'; break;
                        case 'transfer_out': label = 'Transfer'; break;
                        case 'withdrawal': label = 'Withdraw'; break;
                        case 'loanpayment': label = 'Loan Payment'; break;
                        default: label = entry.type;
                    }
                    labels.push(label);
                    values.push(parseFloat(entry.total));
                });

                if (values.length === 0) {
                    document.querySelector("#pieChart").innerHTML = "<p>No transaction data available.</p>";
                    return;
                }

                const options = {
                    chart: {
                        type: 'pie',
                        height: 350
                    },
                    series: values,
                    labels: labels,
                    title: {
                        text: ' '
                    },
                    colors: ['#16DBCC', '#0052CC', '#5243AA', '#00B8D9'],
                };

                const pieChart = new ApexCharts(document.querySelector("#pieChart"), options);
                pieChart.render();
            })
            .catch(error => {
                console.error('Error loading pie chart data:', error);
            });
    });

    // Balance Over Time Area Chart
    document.addEventListener("DOMContentLoaded", function () {
        fetch('get_balance_history.php')
            .then(response => response.json())
            .then(balanceData => {
                const options = {
                    chart: {
                        type: 'area',
                        height: 450,
                        width: '100%',
                    },
                    series: [{
                        name: 'Total Balance',
                        data: balanceData
                    }],
                    title: {
                        text: '',
                        style: {
                            fontSize: '20px'
                        }
                    },
                    colors: ['#00bfff'],
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    xaxis: {
                        type: 'datetime',
                        title: {
                            text: 'Date & Time',
                            style: {
                                fontSize: '10px',
                            }
                        },
                        labels: {
                            datetimeUTC: false,
                            format: 'MMM dd, HH:mm',
                            formatter: function (value) {
                                const date = new Date(value);
                                const utcOffset = date.getTimezoneOffset() * 60000; // Convert offset to milliseconds
                                const localDate = new Date(date.getTime() + utcOffset + (8 * 3600000)); // Add 8 hours for Asia/Manila
                                const options = { month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit', hour12: false };
                                return new Intl.DateTimeFormat('en-US', options).format(localDate);
                            }
                        },
                    },
                    yaxis: {
                        title: {
                            text: 'Amount (₱)'
                        }
                    },
                    grid: {
                        show: true,
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
                    }
                };

                const chart = new ApexCharts(document.querySelector("#balancechart"), options);
                chart.render();
            })
            .catch(error => {
                console.error('Error loading balance history:', error);
            });
    });

    
    </script>
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
    <!-- <script src="../assets/js/Userdash.js"></script> -->
    <script>
    // Add this before your existing scripts
    document.addEventListener('DOMContentLoaded', function() {
        // Get all tabs and transaction rows
        const tabs = document.querySelectorAll('.transactions-tabs .tab');
        const rows = document.querySelectorAll('.transaction-row');

        // Add click event listener to each tab
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');

                // Get the type of transactions to show
                const type = this.getAttribute('data-type');

                // Show/hide rows based on type
                rows.forEach(row => {
                    const rowType = row.getAttribute('data-type');
                    if (type === 'all') {
                        row.style.display = '';
                    } else if (type === 'transfer') {
                        // Show both transfer_in and transfer_out for transfer tab
                        if (rowType === 'transfer_in' || rowType === 'transfer_out') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        row.style.display = rowType === type ? '' : 'none';
                    }
                });
            });
        });
    });

    // Your existing scripts...
</script>
<script src="../assets/js/session.js"></script>
</body>
</html>
