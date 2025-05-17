<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/otp.php';

redirectIfNotLoggedIn();  // Ensure user is logged in

$userId = $_SESSION['user_id'];
$error = '';
$success = '';
$otpSent = false;
$balance = 0;

// Get user's account balance
$stmt = $pdo->prepare("SELECT balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$balance = $stmt->fetchColumn();

// Handle withdrawal form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['amount'])) {
        $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Validate amount
        if ($amount <= 0) {
            $error = "Amount must be greater than 0.";
        } elseif ($amount > $balance) {
            $error = "Insufficient balance.";
        } else {
            // Get user's email for OTP generation
            $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $email = $user['email'];

            // Generate OTP and send to email
            if ($email && generateOTP($email)) {
                // Store pending withdrawal in session
                $_SESSION['pending_withdrawal'] = [
                    'amount' => $amount
                ];

                // Redirect to OTP verification page
                header("Location: ../otp-verification.php?type=withdraw");
                exit();
            } else {
                $error = "Failed to send OTP. Please try again.";
            }
        }
    }
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
$profilePic = $user['profile_picture'] ? '../uploads/' . $user['profile_picture'] : '../assets/images/default-avatar.png';
// Fetch user's profile information


// Get recent transactions
$stmt = $pdo->prepare("
    SELECT * 
FROM transactions  
WHERE type = 'withdrawal' 
  AND account_id = (SELECT account_id FROM accounts WHERE user_id = ?) 
ORDER BY created_at DESC;

");
$stmt->execute([$userId]);
$transactions = $stmt->fetchAll();

// Get account ID of the current user
$stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$accountId = $stmt->fetchColumn();

// Total withdrawals this month
$stmt = $pdo->prepare("SELECT SUM(amount) FROM transactions WHERE type = 'withdrawal' AND account_id = ? AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
$stmt->execute([$accountId]);
$monthlyTotal = $stmt->fetchColumn() ?: 0;

// Largest single withdrawal
$stmt = $pdo->prepare("SELECT MAX(amount) FROM transactions WHERE type = 'withdrawal' AND account_id = ?");
$stmt->execute([$accountId]);
$largestDeposit = $stmt->fetchColumn() ?: 0;

// Average weekly withdrawal
$stmt = $pdo->prepare("
    SELECT AVG(weekly_sum) 
    FROM (
        SELECT SUM(amount) AS weekly_sum
        FROM transactions
        WHERE type = 'withdrawal' AND account_id = ?
        GROUP BY YEARWEEK(created_at)
    ) AS weekly_totals
");
$stmt->execute([$accountId]);
$averageWeeklyDeposit = $stmt->fetchColumn() ?: 0;

// Total withdrawal this week
$stmt = $pdo->prepare("
    SELECT SUM(amount) 
    FROM transactions 
    WHERE type = 'withdrawal' 
      AND account_id = ? 
      AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
");
$stmt->execute([$accountId]);
$weeklyDeposits = $stmt->fetchColumn() ?: 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus-Banksystem - Withdraw</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/withdraw.css">

    <script src="../assets/js/navhover.js"></script>
    <script src="../assets/js/sidebar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
                <img src="../assets/images/inactive-dashboard.png" alt="dashboard-logo" class="nav-icon" data-default="../assets/images/inactive-dashboard.png" data-hover="../assets/images/hover-dashboard.png"> 
                Dashboard
            </a>

            <a href="deposit.php" class="btn">
                <img src="../assets/images/inactive-deposit.png" alt="deposit-logo" class="nav-icon" data-default="../assets/images/inactive-deposit.png" data-hover="../assets/images/hover-deposit.png"> 
                Deposit
            </a>

            <a href="withdraw.php" class="btn dash-text">
                <img src="../assets/images/hover-withdraw.png" alt="withdraw-logo" class="nav-icon" data-default="../assets/images/hover-withdraw.png" data-hover="../assets/images/hover-withdraw.png"> 
                Withdraw
            </a>

            <a href="transfer.php" class="btn">
                <img src="../assets/images/inactive-transfer.png" alt="transfer-logo" class="nav-icon" data-default="../assets/images/inactive-transfer.png" data-hover="../assets/images/hover-transfer.png"> 
                Transfer
            </a>

            <a href="transactions.php" class="btn">
                <img src="../assets/images/inactive-transaction.png" alt="transactions-logo" class="nav-icon" data-default="../assets/images/inactive-transaction.png" data-hover="../assets/images/hover-transaction.png"> 
                Transactions
            </a>

            <a href="investment.php" class="btn">
                <img src="../assets/images/inactive-investment.png" alt="investment-logo" class="nav-icon" data-default="../assets/images/inactive-investment.png" data-hover="../assets/images/hover-investment.png"> 
                Investment
            </a>

            <a href="loan.php" class="btn">
                <img src="../assets/images/inactive-loans.png" alt="loans-logo" class="nav-icon" data-default="../assets/images/inactive-loans.png" data-hover="../assets/images/hover-loans.png"> 
                Loans
            </a>
        </nav>       
<hr>
        <div class="logout-cont">
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </aside>

    <main class="container">
        <header>
            <h1>Withdraw Funds</h1>
            <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>

        <nav class="dashboard-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="deposit.php">Deposit</a>
            <a href="withdraw.php" class="active">Withdraw</a>
            <a href="transfer.php">Transfer</a>
            <a href="transactions.php">Transactions</a>
        </nav>

    <div class="wrap">
    
            <div class="content">
                <div style="width: 100%;">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <h2>Withdraw Money</h2>
                <div class="balance-info">
                    <p>Current Balance: <strong>$<?= number_format($balance, 2) ?></strong></p>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label>Amount to Withdraw</label>
                        <input type="number" name="amount" step="0.01" min="0.01" required>
                    </div>

                    <button type="submit" class="btn">Withdraw</button>
                </form>
             </div>
            </div>

            <div class="Summary">
                <div style="width: 100%;">
                    <h2>Quick Summary</h2>
                    <ul>
                        <li>Total Withdrawals This Month: <strong>$<?= number_format($monthlyTotal, 2) ?></strong></li>
                        <li>Largest Withdraw: <strong>$<?= number_format($largestDeposit, 2) ?></strong></li>
                        <li>Average Weekly withdrawal: <strong>$<?= number_format($averageWeeklyDeposit, 2) ?></strong></li>
                        <li>Withdrawal this week: <strong>$<?= number_format($weeklyDeposits, 2) ?></strong> / $100,000 limit</li>
                    </ul>
                  </div>
              </div>
          </div>

        <div class="deposit-distribution-chart content-1">
                <h2>Withdrawal Rate</h2>
                <div id="monthlyDepositChart"></div>
            </div>

            <div class="transactions-table-wrapper content-1">
                <table class="transactions-table">
                <thead>
                    <tr>
                    <th></th>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Type</th>
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

                    <td><?= date('j M, g:i A', strtotime($txn['created_at'])) ?></td>
                    <td><?= htmlspecialchars($txn['transaction_id']) ?></td>
                    <td><?= ucfirst($txn['type']) ?></td>
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
    </main>
</div>
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
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'submit'];
        events.forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        // Add form submit handler
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Reset timer but don't prevent form submission
                resetInactivityTimer();
            });
        }

        // Initial timer start
        resetInactivityTimer();
    });
    </script>

    <!-- Apexchart Analyticcs bar chart -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
  fetch('get_monthly_withdraw.php')
    .then(res => res.json())
    .then(data => {
      // Build a lookup of the returned months → totals
      const depositMap = data.reduce((acc, { month, total_deposit }) => {
        acc[month] = parseFloat(total_deposit);
        return acc;
      }, {});

      // Define all months and map to your fetched data (or 0)
      const year = new Date().getFullYear();
      const monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      const categories = monthNames;
      const totals = monthNames.map((_, idx) => {
        const key = `${year}-${String(idx + 1).padStart(2, '0')}`;  // e.g. “2025-04”
        return depositMap[key] || 0;
      });

      // ApexCharts options with responsive breakpoints
      const options = {
        chart: {
          type: 'bar',
          width: '100%',
          height: 400,
          toolbar: {
            show: true,
            tools: { download: true, zoom: true, reset: true }
          },
          dropShadow: { enabled: false },
          fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
          bar: {
            borderRadius: 6,
            columnWidth: '50%',
            distributed: false
          }
        },
        dataLabels: {
          enabled: true,
          formatter: val => val.toLocaleString(),
          style: {
            fontSize: '0px',        // hide on small bars
            colors: ['#fff']
          }
        },
        fill: {
          colors: ['#16DBCC'],
          opacity: 0.85
        },
        stroke: {
          show: true,
          width: 1,
          colors: ['#fff']
        },
        series: [{
          name: 'Monthly Withdrawals',
          type: 'column',
          color: '#16DBCC',
          data: totals
        }],
        xaxis: {
          categories,
          title: {
            text: 'Month',
            style: { color: '#333', fontSize: '13px' }
          },
          labels: {
            rotate: -45,
            style: { colors: '#333', fontSize: '12px' }
          }
        },
        yaxis: {
          title: {
            text: 'Total Withdraws',
            style: { color: '#333', fontSize: '13px' }
          },
          labels: {
            formatter: val => val.toLocaleString(),
            style: { colors: '#333', fontSize: '12px' }
          }
        },
        title: {
          text: `Withdraws in ${year}`,
          align: 'center',
          style: { fontSize: '16px', color: '#222' }
        },
        tooltip: {
          y: {
            formatter: val => `₱ ${val.toLocaleString()}`
          }
        },
        grid: {
          borderColor: '#ececec',
          strokeDashArray: 4
        },
        responsive: [
          {
            breakpoint: 768,  // <768px
            options: {
              plotOptions: { bar: { columnWidth: '70%' } },
              dataLabels: { enabled: false },
              xaxis: {
                labels: {
                  rotate: -60,
                  style: { fontSize: '10px' }
                }
              },
              yaxis: {
                labels: { style: { fontSize: '10px' } }
              }
            }
          },
          {
            breakpoint: 480,  // <480px
            options: {
              plotOptions: { bar: { columnWidth: '100%' } },
              dataLabels: { enabled: false },
              xaxis: {
                labels: {
                  rotate: -90,
                  hideOverlappingLabels: true,
                  style: { fontSize: '8px' }
                }
              },
              yaxis: {
                labels: { style: { fontSize: '8px' } }
              },
              tooltip: { enabled: true }
            }
          }
        ]
      };

      // Render
      const chartEl = document.querySelector("#monthlyDepositChart");
      if (chartEl) {
        const chart = new ApexCharts(chartEl, options);
        chart.render();
      }
    })
    .catch(err => console.error('Error fetching deposit data:', err));
});


</script>
</html>
