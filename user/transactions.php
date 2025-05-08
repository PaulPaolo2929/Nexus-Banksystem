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

// Get transactions
$stmt = $pdo->prepare("
    SELECT t.*, a.account_number as related_account_number
    FROM transactions t
    LEFT JOIN accounts a ON t.related_account_id = a.account_id
    WHERE t.account_id = ?
    ORDER BY t.created_at DESC
");
$stmt->execute([$accountId]);
$transactions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureBank - Transactions</title>
    <link rel="stylesheet" href="../assets/css/.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>/* General Reset and Fonts */
/* General Reset and Fonts */
/*———— Global & Reset ————*/
*,
*::before,
*::after {
  box-sizing: border-box;
}
html {
  scroll-behavior: smooth;
}
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background-color: #f4f7fe;
  color: #2e3a59;
  opacity: 0;
  animation: fadeIn 0.8s ease-out forwards;
}

/*———— Keyframe Animations ————*/
@keyframes fadeIn {
  to { opacity: 1; }
}
@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to   { transform: translateY(0);   opacity: 1; }
}

/*———— Container & Layout ————*/
.container {
  padding: 20px 40px;
  max-width: 1200px;
  margin: auto;
  animation: slideUp 0.6s ease-out 0.2s both;
}

/*———— Header ————*/
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  padding-bottom: 20px;
}
header h1 {
  font-size: 28px;
  font-weight: 600;
  margin: 0;
  animation: slideUp 0.6s ease-out 0.4s both;
}
.logout {
  margin-top: 10px;
  text-decoration: none;
  color: #fff;
  background-color: #4a6cf7;
  padding: 10px 16px;
  border-radius: 8px;
  font-weight: 500;
  transition: background-color 0.3s, transform 0.3s;
  animation: slideUp 0.6s ease-out 0.5s both;
}
.logout:hover {
  background-color: #3a57e8;
  transform: translateY(-2px);
}

/*———— Nav ————*/
.dashboard-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 30px;
  border-bottom: 2px solid #e0e6f7;
  padding-bottom: 10px;
  animation: slideUp 0.6s ease-out 0.6s both;
}
.dashboard-nav a {
  position: relative;
  text-decoration: none;
  color: #5c6b8a;
  font-weight: 500;
  padding: 10px 14px;
  border-radius: 10px;
  transition: background-color 0.2s, color 0.2s;
}
.dashboard-nav a.active::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 10%;
  width: 80%;
  height: 3px;
  background: #4a6cf7;
  border-radius: 2px;
  animation: fadeIn 0.3s ease-out 0.8s both;
}
.dashboard-nav a:hover,
.dashboard-nav a.active {
  background-color: #e6ecff;
  color: #4a6cf7;
}

/*———— Cards & Charts ————*/
.weekly-activity-chart,
.content {
  background-color: #fff;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 30px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
  transform: translateY(20px);
  opacity: 0;
  animation: slideUp 0.6s ease-out 0.7s both;
}
.weekly-activity-chart h2,
.content h2 {
  font-size: 20px;
  margin: 0 0 20px;
  position: relative;
}
.weekly-activity-chart h2::after,
.content h2::after {
  content: '';
  display: block;
  width: 50px;
  height: 4px;
  background: #4a6cf7;
  border-radius: 2px;
  margin-top: 8px;
}

/*———— Table ————*/
.table-wrapper {
  overflow-x: auto;
}
.transactions-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 600px;
  transform: translateY(20px);
  opacity: 0;
  animation: slideUp 0.6s ease-out 0.8s both;
}
.transactions-table th,
.transactions-table td {
  padding: 12px 16px;
  text-align: left;
}
.transactions-table th {
  background-color: #f1f5ff;
  color: #6b7a99;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.transactions-table tr:nth-child(even) {
  background-color: #f9fbff;
}
.transactions-table td {
  font-size: 15px;
  border-bottom: 1px solid #e8edf5;
  transition: background-color 0.2s;
}
.transactions-table tbody tr:hover td {
  background-color: #f0f4ff;
}
.text-success {
  color: #20c997;
  font-weight: 600;
}
.text-danger {
  color: #f44336;
  font-weight: 600;
}

/*———— Responsive ————*/
@media (max-width: 1024px) {
  .container { padding: 15px 20px; }
  header h1 { font-size: 24px; }
  .weekly-activity-chart,
  .content { padding: 20px; }
}
@media (max-width: 768px) {
  header { flex-direction: column; align-items: flex-start; }
  .dashboard-nav { gap: 8px; }
  .weekly-activity-chart,
  .content { padding: 16px; }
}
@media (max-width: 480px) {
  .container { padding: 10px 15px; }
  header h1 { font-size: 20px; margin-bottom: 10px; }
  .logout { width: 100%; text-align: center; }
  .dashboard-nav a { padding: 8px 12px; font-size: 14px; }
  .weekly-activity-chart h2,
  .content h2 { font-size: 18px; }
  .transactions-table th,
  .transactions-table td {
    padding: 8px 10px;
    font-size: 13px;
  }
}

</style>

</head>
<body>
    <div class="container">
        <header>
            <h1>Transaction History</h1>
            <a href="../logout.php" class="logout">Logout</a>
        </header>
        
        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="deposit.php">Deposit</a>
            <a href="withdraw.php">Withdraw</a>
            <a href="transfer.php">Transfer</a>
            <a href="transactions.php" class="active">Transactions</a>
        </nav>

        
        
        <div class="weekly-activity-chart">
                <h2>Weekly Activity </h2>
                <div id="Transchart"></div>
            </div>

        <div class="content">
            <h2>All Transactions</h2>
            
            <?php if (empty($transactions)): ?>
                <p>No transactions found.</p>
            <?php else: ?>
                <div class="table-wrapper">
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Related Account</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?= date('M j, Y H:i', strtotime($txn['created_at'])) ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $txn['type'])) ?></td>
                                <td class="<?= in_array($txn['type'], ['deposit', 'transfer_in']) ? 'text-success' : 'text-danger' ?>">
                                    $<?= number_format($txn['amount'], 2) ?>
                                </td>
                                <td><?= htmlspecialchars($txn['description']) ?></td>
                                <td><?= $txn['related_account_number'] ?: 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php endif; ?>
        </div>
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
</html>
