<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/otp.php';
require_once '../includes/session_manager.php';

redirectIfNotLoggedIn();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';
$weeklyLimit = 100000.00;

// Get user's account and balance
$stmt = $pdo->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ?");
$stmt->execute([$userId]);
$account = $stmt->fetch();

if ($account) {
    $accountId = $account['account_id'];
    $balance = $account['balance'];

    // Total deposited in last 7 days
    $stmt = $pdo->prepare("
        SELECT SUM(amount) FROM transactions 
        WHERE account_id = ? AND type = 'deposit' AND created_at >= NOW() - INTERVAL 7 DAY
    ");
    $stmt->execute([$accountId]);
    $weeklyDeposits = $stmt->fetchColumn() ?: 0;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['amount'])) {
            $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            if ($amount < 100) {
                $error = "Minimum deposit amount is ₱100.";
            } elseif ($amount <= 0) {
                $error = "Amount must be greater than 0.";
            } elseif (($weeklyDeposits + $amount) > $weeklyLimit) {
                $remaining = $weeklyLimit - $weeklyDeposits;
                $error = "Weekly deposit limit exceeded. You can only deposit ₱" . number_format($remaining, 2) . " more this week.";
            } else {
                // Get user's email
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                $email = $user['email'];

                // Generate and send OTP
                if ($email && generateOTP($email)) {
                    $_SESSION['pending_deposit'] = [
                        'account_id' => $accountId,
                        'amount' => $amount
                    ];

                    header("Location: ../otp-verification.php?type=deposit");
                    exit();
                } else {
                    $error = "Failed to send OTP. Please try again later.";
                }
            }
        }
    }
} else {
    $error = "Account not found.";
    $balance = 0;
    $weeklyDeposits = 0;
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
$profilePic = $user['profile_picture'] ? '../uploads/' . $user['profile_picture'] : '../assets/images/default-avatars.png';
// Fetch user's profile information


// Get recent transactions
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM transactions  
    WHERE type = 'deposit' 
      AND account_id = (SELECT account_id FROM accounts WHERE user_id = ?)
");
$stmt->execute([$userId]);
$totalTransactions = $stmt->fetchColumn();

// Calculate pagination
$transactionsPerPage = 10;
$totalPages = ceil($totalTransactions / $transactionsPerPage);
$currentPage = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$offset = ($currentPage - 1) * $transactionsPerPage;

$stmt = $pdo->prepare("
    SELECT * 
    FROM transactions  
    WHERE type = 'deposit' 
      AND account_id = (SELECT account_id FROM accounts WHERE user_id = ?) 
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$userId, $transactionsPerPage, $offset]);
$transactions = $stmt->fetchAll();

// Calculate total deposits for the current month
$stmt = $pdo->prepare("
    SELECT SUM(amount) FROM transactions 
    WHERE account_id = ? 
      AND type = 'deposit' 
      AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
      AND YEAR(created_at) = YEAR(CURRENT_DATE())
");
$stmt->execute([$accountId]);
$monthlyTotal = $stmt->fetchColumn() ?: 0;

// Get the largest deposit
$stmt = $pdo->prepare("
    SELECT MAX(amount) FROM transactions 
    WHERE account_id = ? AND type = 'deposit'
");
$stmt->execute([$accountId]);
$largestDeposit = $stmt->fetchColumn() ?: 0;

// Calculate average weekly deposit (over last 4 weeks)
$stmt = $pdo->prepare("
    SELECT SUM(amount) / 4 FROM transactions 
    WHERE account_id = ? 
      AND type = 'deposit' 
      AND created_at >= NOW() - INTERVAL 28 DAY
");
$stmt->execute([$accountId]);
$averageWeeklyDeposit = $stmt->fetchColumn() ?: 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus-Banksystem - Deposit</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/deposit.css">

    <!-- sciprts -->
    <script src="../assets/js/navhover.js"></script>
    <script src="../assets/js/sidebar.js"></script>

    <!-- Apexchart -->
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
                <img src="../assets/images/inactive-dashboard.png" alt="dashboard-logo" class="nav-icon"
                     data-default="../assets/images/inactive-dashboard.png"
                     data-hover="../assets/images/hover-dashboard.png"> 
                Dashboard
            </a>
            <a href="deposit.php" class="btn dash-text">
                <img src="../assets/images/hover-deposit.png" alt="deposit-logo" class="nav-icon"
                     data-default="../assets/images/hover-deposit.png"
                     data-hover="../assets/images/hover-deposit.png"> 
                Deposit
            </a>
            <a href="withdraw.php" class="btn">
                <img src="../assets/images/inactive-withdraw.png" alt="withdraw-logo" class="nav-icon"
                     data-default="../assets/images/inactive-withdraw.png"
                     data-hover="../assets/images/hover-withdraw.png"> 
                Withdraw
            </a>
            <a href="transfer.php" class="btn">
                <img src="../assets/images/inactive-transfer.png" alt="transfer-logo" class="nav-icon"
                     data-default="../assets/images/inactive-transfer.png"
                     data-hover="../assets/images/hover-transfer.png"> 
                Transfer
            </a>
            <a href="transactions.php" class="btn">
                <img src="../assets/images/inactive-transaction.png" alt="transactions-logo" class="nav-icon"
                     data-default="../assets/images/inactive-transaction.png"
                     data-hover="../assets/images/hover-transaction.png"> 
                Transactions
            </a>
            <a href="investment.php" class="btn">
                <img src="../assets/images/inactive-investment.png" alt="investment-logo" class="nav-icon"
                     data-default="../assets/images/inactive-investment.png"
                     data-hover="../assets/images/hover-investment.png"> 
                Investment
            </a>
            <a href="loan.php" class="btn">
                <img src="../assets/images/inactive-loans.png" alt="loans-logo" class="nav-icon"
                     data-default="../assets/images/inactive-loans.png"
                     data-hover="../assets/images/hover-loans.png"> 
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
            <h1>Deposit Funds</h1>
            <button class="hamburger">&#9776;</button> <!-- Hamburger icon -->
        </header>

        <div class="wrap">
              <div class="content">
                <div style="width: 100%;">
                  <?php if ($error): ?>
                      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                  <?php endif; ?>

                  <?php if ($success): ?>
                      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                  <?php endif; ?>
                    <h2>Deposit Money</h2>
                  <div class="balance-info">
                      <p>Current Balance: <strong>₱<?= number_format($balance, 2) ?></strong></p>   
                  </div>

                  <form method="POST">
                      <div class="form-group">
                          <label>Amount to Deposit</label>
                          <input type="number" name="amount" step="0.01" min="0.01" required>
                      </div>

                      <button type="submit" class="btn">Deposit</button>
                  </form> 
                  </div>  
              </div>

              <div class="Summary">
                <div style="width: 100%;">
                    <h2>Quick Summary</h2>
                    <ul>
                        <li>Total Deposits This Month: <strong>₱<?= number_format($monthlyTotal, 2) ?></strong></li>
                        <li>Largest Deposit: <strong>₱<?= number_format($largestDeposit, 2) ?></strong></li>
                        <li>Average Weekly Deposit: <strong>₱<?= number_format($averageWeeklyDeposit, 2) ?></strong></li>
                        <li>Deposited this week: <strong>₱<?= number_format($weeklyDeposits, 2) ?></strong> / ₱100,000 limit</li>
                    </ul>
                  </div>
              </div>
        </div>

          <div class="deposit-distribution-chart content-1">
                <h2>Deposit Rate</h2>
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

                  <!-- Pagination Controls -->
                  <?php if ($totalPages > 1): ?>
                  <div class="pagination">
                      <?php if ($currentPage > 1): ?>
                          <a href="?page=<?= $currentPage - 1 ?>" class="page-link">&laquo; Previous</a>
                      <?php endif; ?>
                      
                      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                          <a href="?page=<?= $i ?>" 
                             class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                              <?= $i ?>
                          </a>
                      <?php endfor; ?>
                      
                      <?php if ($currentPage < $totalPages): ?>
                          <a href="?page=<?= $currentPage + 1 ?>" class="page-link">Next &raquo;</a>
                      <?php endif; ?>
                  </div>
                  <?php endif; ?>
              </div>
                       
    </main>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
  fetch('get_monthly_deposits.php')
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
        const key = `${year}-${String(idx + 1).padStart(2, '0')}`;  // e.g. "2025-04"
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
          name: 'Monthly Deposit',
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
            text: 'Total Deposits',
            style: { color: '#333', fontSize: '13px' }
          },
          labels: {
            formatter: val => val.toLocaleString(),
            style: { colors: '#333', fontSize: '12px' }
          }
        },
        title: {
          text: `Deposits in ${year}`,
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
<script src="../assets/js/session.js"></script>
</body>
</html>
