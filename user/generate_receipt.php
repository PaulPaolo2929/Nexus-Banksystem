<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../vendor/autoload.php'; // Make sure to install TCPDF via composer

// Increase memory limit for PDF generation
ini_set('memory_limit', '256M');

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

if (!isset($_GET['transaction_id'])) {
    http_response_code(400);
    exit('Transaction ID required');
}

$userId = $_SESSION['user_id'];
$transactionId = $_GET['transaction_id'];

// Get transaction details
$stmt = $pdo->prepare("
    SELECT t.*, a1.account_number as from_account, a2.account_number as to_account,
           u.full_name, u.email
    FROM transactions t
    JOIN accounts a1 ON t.account_id = a1.account_id
    LEFT JOIN accounts a2 ON t.related_account_id = a2.account_id
    JOIN users u ON a1.user_id = u.user_id
    WHERE t.transaction_id = ? AND a1.user_id = ?
");

$stmt->execute([$transactionId, $userId]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    http_response_code(404);
    exit('Transaction not found');
}

// Create new PDF document with optimized settings
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Disable image compression to save memory
$pdf->setJPEGQuality(75);
$pdf->setImageScale(1);

// Set document information
$pdf->SetCreator('Nexus Bank');
$pdf->SetAuthor('Nexus Bank');
$pdf->SetTitle('Transaction Receipt');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Add logo - with smaller dimensions
$pdf->Image('../assets/images/Logo-color.png', 15, 15, 30);

// Add content
$pdf->Cell(0, 25, '', 0, 1); // Reduced space after logo

$pdf->SetFont('helvetica', 'B', 18); // Slightly smaller font
$pdf->Cell(0, 10, 'Transaction Receipt', 0, 1, 'C');
$pdf->Cell(0, 5, '', 0, 1); // Reduced space

$pdf->SetFont('helvetica', '', 11); // Slightly smaller font

// Common details
$pdf->Cell(0, 8, 'Transaction ID: ' . $transaction['transaction_id'], 0, 1);
$pdf->Cell(0, 8, 'Date: ' . date('F j, Y, g:i a', strtotime($transaction['created_at'])), 0, 1);
$pdf->Cell(0, 8, 'Amount: $' . number_format($transaction['amount'], 2), 0, 1);

// Dynamic content based on transaction type
switch ($transaction['type']) {
    case 'deposit':
        $pdf->Cell(0, 8, 'Transaction Type: Deposit', 0, 1);
        $pdf->Cell(0, 8, 'Recipient: ' . $transaction['full_name'], 0, 1);
        $pdf->Cell(0, 8, 'Account Number: ' . $transaction['from_account'], 0, 1);
        break;

    case 'withdrawal':
        $pdf->Cell(0, 8, 'Transaction Type: Withdrawal', 0, 1);
        $pdf->Cell(0, 8, 'Sender: ' . $transaction['full_name'], 0, 1);
        $pdf->Cell(0, 8, 'Account Number: ' . $transaction['from_account'], 0, 1);
        break;

    case 'transfer_in':
        $pdf->Cell(0, 8, 'Transaction Type: Transfer In', 0, 1);
        $pdf->Cell(0, 8, 'Recipient: ' . $transaction['full_name'], 0, 1);
        $pdf->Cell(0, 8, 'From Account: ' . ($transaction['to_account'] ?: 'N/A'), 0, 1);
        $pdf->Cell(0, 8, 'To Account: ' . $transaction['from_account'], 0, 1);
        break;

    case 'transfer_out':
        $pdf->Cell(0, 8, 'Transaction Type: Transfer Out', 0, 1);
        $pdf->Cell(0, 8, 'Sender: ' . $transaction['full_name'], 0, 1);
        $pdf->Cell(0, 8, 'From Account: ' . $transaction['from_account'], 0, 1);
        $pdf->Cell(0, 8, 'To Account: ' . ($transaction['to_account'] ?: 'N/A'), 0, 1);
        break;

    default:
        $pdf->Cell(0, 8, 'Transaction Type: ' . ucfirst($transaction['type']), 0, 1);
        $pdf->Cell(0, 8, 'Account Number: ' . $transaction['from_account'], 0, 1);
        break;
}

if ($transaction['description']) {
    $pdf->Cell(0, 8, 'Description: ' . $transaction['description'], 0, 1);
}

$pdf->Cell(0, 8, '', 0, 1); // Reduced space

// Add footer text
$pdf->SetFont('helvetica', 'I', 9); // Slightly smaller font
$pdf->Cell(0, 8, 'This is an official receipt from Nexus Bank.', 0, 1, 'C');
$pdf->Cell(0, 8, 'For any queries, please contact our support.', 0, 1, 'C');

// Clean up any output that might have been sent
if (ob_get_length()) {
    ob_end_clean();
}

// Output PDF with compression
$pdf->Output('Transaction_Receipt_' . $transactionId . '.pdf', 'D', true);
