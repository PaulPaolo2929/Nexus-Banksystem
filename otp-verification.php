<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/otp.php';

$type = $_GET['type'] ?? $_POST['type'] ?? 'register';

$redirect = false;
switch ($type) {
    case 'register':
        $redirect = !isset($_SESSION['temp_email']);
        break;
    case 'login':
        $redirect = !isset($_SESSION['temp_user_id']);
        break;
    case 'transfer':
        $redirect = !isset($_SESSION['pending_transfer']) || !isset($_SESSION['user_id']);
        break;
    case 'withdraw':
        $redirect = !isset($_SESSION['pending_withdrawal']) || !isset($_SESSION['user_id']);
        break;
    case 'deposit':
        $redirect = !isset($_SESSION['pending_deposit']) || !isset($_SESSION['user_id']);
        break;
    default:
        $redirect = true;
        break;
}

if ($redirect) {
    header("Location: login.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedOTP = $_POST['otp'] ?? '';

    try {
        if ($type === 'register') {
            $email = $_SESSION['temp_email'] ?? null;

            if (!$email || !verifyOTP($email, $submittedOTP)) {
                $error = "Invalid OTP or session expired";
            } else {
                $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    $accountNumber = generateAccountNumber();
                    $pdo->prepare("INSERT INTO accounts (user_id, account_number) VALUES (?, ?)")
                        ->execute([$user['user_id'], $accountNumber]);

                    $_SESSION['user_id'] = $user['user_id'];
                    unset($_SESSION['temp_email']);

                    header("Location: user/dashboard.php");
                    exit();
                } else {
                    $error = "User registration incomplete.";
                }
            }

        } elseif ($type === 'login') {
            $user_id = $_SESSION['temp_user_id'] ?? null;
            $is_admin = $_SESSION['temp_is_admin'] ?? false;

            if (!$user_id) {
                $error = "Session expired. Please login again.";
            } else {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && verifyOTP($user['email'], $submittedOTP)) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['is_admin'] = $is_admin;
                    unset($_SESSION['temp_user_id'], $_SESSION['temp_is_admin']);

                    header("Location: " . ($is_admin ? "admin/dashboard.php" : "user/dashboard.php"));
                    exit();
                } else {
                    $error = "Invalid OTP.";
                }
            }

        } elseif ($type === 'transfer') {
            $user_id = $_SESSION['user_id'] ?? null;

            if (!$user_id || !isset($_SESSION['pending_transfer'])) {
                $error = "Session expired. Please try again.";
            } else {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && verifyOTP($user['email'], $submittedOTP)) {
                    $transfer = $_SESSION['pending_transfer'];
                    unset($_SESSION['pending_transfer']);

                    try {
                        $pdo->beginTransaction();

                        $stmt = $pdo->prepare("SELECT account_id, balance, account_number FROM accounts WHERE user_id = ? FOR UPDATE");
                        $stmt->execute([$user_id]);
                        $fromAccount = $stmt->fetch();

                        $stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE account_number = ? FOR UPDATE");
                        $stmt->execute([$transfer['to_account']]);
                        $recipientAccount = $stmt->fetch();

                        if (!$fromAccount || !$recipientAccount) {
                            throw new Exception("Invalid accounts.");
                        }

                        if ($fromAccount['account_number'] === $transfer['to_account']) {
                            throw new Exception("Cannot transfer to your own account.");
                        }

                        if ((float)$fromAccount['balance'] < $transfer['amount']) {
                            throw new Exception("Insufficient funds.");
                        }

                        $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?")
                            ->execute([$transfer['amount'], $fromAccount['account_id']]);

                        $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?")
                            ->execute([$transfer['amount'], $recipientAccount['account_id']]);

                        $descOut = $transfer['description'] ?: "Transfer to {$transfer['to_account']}";
                        $descIn = $transfer['description'] ?: "Transfer from {$fromAccount['account_number']}";

                        $pdo->prepare("INSERT INTO transactions (account_id, type, amount, description, related_account_id) VALUES (?, 'transfer_out', ?, ?, ?)")
                            ->execute([$fromAccount['account_id'], $transfer['amount'], $descOut, $recipientAccount['account_id']]);

                        $pdo->prepare("INSERT INTO transactions (account_id, type, amount, description, related_account_id) VALUES (?, 'transfer_in', ?, ?, ?)")
                            ->execute([$recipientAccount['account_id'], $transfer['amount'], $descIn, $fromAccount['account_id']]);

                        $pdo->commit();

                        $_SESSION['flash_success'] = "Successfully transferred $" . number_format($transfer['amount'], 2) . " to account {$transfer['to_account']}";
                        header("Location: user/transfer.php");
                        exit();

                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = "Transfer failed: " . $e->getMessage();
                    }
                } else {
                    $error = "Invalid OTP.";
                }
            }

        } elseif ($type === 'withdraw') {
            $user_id = $_SESSION['user_id'] ?? null;

            if (!$user_id || !isset($_SESSION['pending_withdrawal'])) {
                $error = "Session expired. Please try again.";
            } else {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && verifyOTP($user['email'], $submittedOTP)) {
                    $withdrawal = $_SESSION['pending_withdrawal'];
                    unset($_SESSION['pending_withdrawal']);

                    try {
                        $pdo->beginTransaction();

                        $stmt = $pdo->prepare("SELECT account_id, balance FROM accounts WHERE user_id = ? FOR UPDATE");
                        $stmt->execute([$user_id]);
                        $account = $stmt->fetch();

                        if ((float)$account['balance'] < $withdrawal['amount']) {
                            throw new Exception("Insufficient funds.");
                        }

                        $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE account_id = ?")
                            ->execute([$withdrawal['amount'], $account['account_id']]);

                        $desc = "Withdrawal of {$withdrawal['amount']}";
                        $pdo->prepare("INSERT INTO transactions (account_id, type, amount, description) VALUES (?, 'withdrawal', ?, ?)")
                            ->execute([$account['account_id'], $withdrawal['amount'], $desc]);

                        $pdo->commit();

                        $_SESSION['flash_success'] = "Successfully withdrew $" . number_format($withdrawal['amount'], 2);
                        header("Location: user/withdraw.php");
                        exit();

                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = "Withdrawal failed: " . $e->getMessage();
                    }
                } else {
                    $error = "Invalid OTP.";
                }
            }

        } elseif ($type === 'deposit') {
            $user_id = $_SESSION['user_id'] ?? null;

            if (!$user_id || !isset($_SESSION['pending_deposit'])) {
                $error = "Session expired. Please try again.";
            } else {
                $stmt = $pdo->prepare("SELECT email FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();

                if ($user && verifyOTP($user['email'], $submittedOTP)) {
                    $deposit = $_SESSION['pending_deposit'];
                    unset($_SESSION['pending_deposit']);

                    try {
                        $pdo->beginTransaction();

                        $stmt = $pdo->prepare("SELECT account_id FROM accounts WHERE user_id = ? FOR UPDATE");
                        $stmt->execute([$user_id]);
                        $account = $stmt->fetch();

                        if (!$account) {
                            throw new Exception("Account not found.");
                        }

                        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE account_id = ?");
                        $stmt->execute([$deposit['amount'], $account['account_id']]);

                        $stmt = $pdo->prepare("INSERT INTO transactions (account_id, type, amount, description) VALUES (?, 'deposit', ?, ?)");
                        $stmt->execute([$account['account_id'], $deposit['amount'], "Cash deposit"]);

                        $pdo->commit();

                        $_SESSION['flash_success'] = "Successfully deposited $" . number_format($deposit['amount'], 2);
                        header("Location: user/deposit.php");
                        exit();
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $error = "Deposit failed: " . $e->getMessage();
                    }
                } else {
                    $error = "Invalid OTP.";
                }
            }
        }
    } catch (PDOException $e) {
        $error = "System error. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OTP Verification - Nexus E‑Banking</title>
  <link rel="stylesheet" href="./assets/css/main.css">
  <link rel="stylesheet" href="./assets/css/otp.css">
</head>
<body>
  <div class="otp-page">
    <img src="./assets/images/Logo.png" alt="Nexus Logo" class="otp-logo">
    <div class="otp-card">

      <h2 class="otp-title">OTP Verification</h2>
      <p class="otp-desc">
        Please enter the OTP (One‑Time Password) sent to your registered email account to complete your verification
      </p>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form id="otp-form" method="POST" novalidate>
        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

        <div class="otp-inputs">
          <?php for ($i = 0; $i < 6; $i++): ?>
            <input
              type="text"
              inputmode="numeric"
              pattern="\d"
              maxlength="1"
              class="otp-input"
              data-index="<?= $i ?>"
            >
          <?php endfor; ?>
        </div>
        <input type="hidden" name="otp" id="otp">

        <div class="timer-resend">
          <div>Remaining time: <span id="countdown">00:60s</span></div>
          <div>Didn’t get the code? 
            <a href="resend-otp.php?type=<?= htmlspecialchars($type) ?>"
               id="resend-link"
               class="disabled"
            >Resend</a>
          </div>
        </div>

        <button type="submit" class="btn-verify">Verify</button>
        <a href="login.php" class="btn-cancel">Cancel</a>
      </form>
    </div>
  </div>

<script>
    // -- Auto‐tab between inputs and collect on submit --
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, i) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g,'').charAt(0) || '';
            if (input.value && i < inputs.length - 1) {
                inputs[i + 1].focus();
            }
        });
        input.addEventListener('keydown', e => {
            if (e.key === 'Backspace' && !input.value && i > 0) {
                inputs[i - 1].focus();
            }
        });
    });

    document.getElementById('otp-form').addEventListener('submit', e => {
        document.getElementById('otp').value =
            Array.from(inputs).map(i => i.value).join('');
    });

    // -- Countdown timer & enable resend --
    let time = 120; // 2 minutes in seconds
    const countdownEl = document.getElementById('countdown');
    const resendLink = document.getElementById('resend-link');
    const timerId = setInterval(() => {
        time--;
        const minutes = Math.floor(time / 60);
        const seconds = time % 60;
        countdownEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0') + 's';
        if (time <= 0) {
            clearInterval(timerId);
            resendLink.classList.remove('disabled');
        }
    }, 1000);
</script>
</body>
</html>
