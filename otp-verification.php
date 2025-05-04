<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/otp.php';

$type = $_GET['type'] ?? $_POST['type'] ?? 'register'; // fallback for POST

// Early session debugging
error_log("OTP Type: " . $type);
error_log("Session data: " . print_r($_SESSION, true));

// Redirect based on missing data for each type
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
    default:
        $redirect = true;
        break;
}

if ($redirect) {
    error_log("Redirecting to login.php — incomplete session data for OTP type: $type");
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
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $error = "System error. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification - SecureBank</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Verify Your Identity</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <p>We've sent a 6-digit code to your <?= $type === 'register' ? 'registered email address' : 'account email' ?>.</p>

        <form method="POST">
            <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
            <div class="form-group">
                <label for="otp">Enter OTP</label>
                <input type="text" id="otp" name="otp" pattern="\d{6}" required autocomplete="one-time-code" inputmode="numeric">
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>

        <p><a href="resend-otp.php?type=<?= htmlspecialchars($type) ?>">Resend OTP</a></p>
    </div>
</body>
</html>
