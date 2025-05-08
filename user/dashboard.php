<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
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

// Get recent transactions
$stmt = $pdo->prepare("
    SELECT * FROM transactions 
    WHERE account_id = (SELECT account_id FROM accounts WHERE user_id = ?)
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus-Banksystem - Dashboard</title>
    <link rel="stylesheet" href="../assets/css/Userdash.css">

    <!-- Apexchart -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
    
       .transaction-distribution-chart, .weekly-activity-chart, .balance-over-time-chart {
    margin-top: 2rem;
    background: #fff;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
    </style>
    
    <!--Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

</head>
<body>

<div class="wrapper">
                <aside>
                       
                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">

                            <nav>
                            <a href="dashboard.php" class="btn"> <img src="../assets/images/Dashboard-logo.png" alt="dashboard-logo"> Dashboard</a>
                                <a href="deposit.php" class="btn"> <img src="../assets/images/Deposit.png" alt="dashboard-logo"> Deposit</a>
                                <a href="withdraw.php" class="btn"> <img src="../assets/images/withdraw.png" alt="dashboard-logo"> Withdraw</a>
                                <a href="transfer.php" class="btn"> <img src="../assets/images/transfer.png" alt="dashboard-logo">Transfer</a>
                                <a href="transactions.php" class="btn"> <img src="../assets/images/transaction-logo.png" alt="Transactions-logo">Transactions</a>
                                <a href="investment.php" class="btn"> <img src="../assets/images/investment-logo.png" alt="dashboard-logo">Investment</a>
                                <a href="loan.php" class="btn"> <img src="../assets/images/loans-logo.png" alt="dashboard-logo">Loans</a>
                                
                            </nav>       

                            <div class="logout-cont">
                                 <a href="../logout.php" class="logout">Logout</a>
                            </div>
                </aside>

                <main class="container">
                    <header>
                        <h1>Overview</h1>
                    </header>
                    
                    
                    <div class="dashboard-content">
                        <div class="account-summary">
                             <h2>Welcome, <?= htmlspecialchars($user['full_name']) ?></h2>
                            <h2>Account Summary</h2>
                            <p>Account Number: <?= htmlspecialchars($user['account_number']) ?></p>
                            <p class="balance">Balance: $<?= number_format($user['balance'], 2) ?></p>
                        </div>
                        

                        <div class="quick-actions">
                            <h2>Quick Actions</h2>
                            <div class="action-buttons">      
                                <a href="deposit.php" class="btn1">Deposit Money</a>
                                <a href="withdraw.php" class="btn1">Withdraw Funds</a>
                                <a href="transfer.php" class="btn1">Transfer Funds</a>
                                <a href="loan.php" class="btn1">Apply for Loan</a>
                                <a href="loan-payment.php" class="btn1">Pay Loan</a>
                            </div>
                        </div>
                        
                        <h2>Recent Transactions</h2>
                        <div class="transactions-container">
                            
                                        ``<div class="transactions-tabs">
                                            <button class="tab active">All Transactions</button>
                                            <button class="tab">Deposit</button>
                                            <button class="tab">Withdraw</button>
                                            <button class="tab">Transfer</button>
                                        </div>

                                        <div class="transactions-table-wrapper">
                                            <table class="transactions-table">
                                            <thead>
                                                <tr>
                                                <th></th>
                                                <th>Description</th>
                                                <th>Transaction ID</th>
                                                <th>Type</th>
                                                <th>Card</th>
                                                <th>Date</th>
                                                <th>Amount</th>
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


                                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                                <td><?= htmlspecialchars($txn['transaction_id']) ?></td>
                                                <td><?= ucfirst($txn['type']) ?></td>
                                                <td><?= htmlspecialchars($txn['transaction_id']) ?></td>
                                                <td><?= date('j M, g:i A', strtotime($txn['created_at'])) ?></td>
                                                <td class="amount <?= in_array($txn['type'],['deposit','transfer_in'])? 'positive':'negative' ?>">
                                                    <?= (in_array($txn['type'],['deposit','transfer_in'])? '+':'−') .
                                                        '$'.number_format($txn['amount'],2) ?>
                                                </td>
                                                <td>
                                                    <button class="btn-download">Download</button>
                                                </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            </table>
                                        </div>
                                </div>

<!-- <div class="recent-transactions">
                            <h2>Recent Transactions</h2>
                            <?php if (empty($transactions)): ?>
                                <p>No transactions found.</p>
                            <?php else: ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $txn): ?>
                                            <tr>
                                                <td><?= date('M j, Y', strtotime($txn['created_at'])) ?></td>
                                                <td><?= ucfirst($txn['type']) ?></td>
                                                <td class="<?= in_array($txn['type'], ['deposit', 'transfer_in']) ? 'text-success' : 'text-danger' ?>">
                                                    $<?= number_format($txn['amount'], 2) ?>
                                                </td>
                                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <a href="transactions.php" class="view-all">View All Transactions</a>
                            <?php endif; ?>
                        </div> -->

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
                    colors: ['#00B8D9', '#0052CC', '#5243AA', '#16DBCC'],
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
                            text: 'Total Balance',
                            style: {
                                fontSize: '20px'
                            }
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
    <!-- <script src="../assets/js/Userdash.js"></script> -->
    
</body>
</html>
