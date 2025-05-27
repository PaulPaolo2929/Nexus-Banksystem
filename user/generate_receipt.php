<?php
// generate_receipt.php

// Show errors for debugging; disable in production
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../vendor/autoload.php';  // if using Composer

// Make sure user is logged in
redirectIfNotLoggedIn();

// Get the transaction ID (and optional type) from the query string
$txnId = $_GET['transaction_id'] ?? '';
if (!$txnId) {
    die('No transaction specified.');
}

// Fetch transaction details (and type) from DB, verifying it belongs to the logged-in user
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT t.*, 
           a.account_number AS related_account_number, 
           u.full_name, 
           me.balance
    FROM transactions t
    JOIN accounts me ON t.account_id = me.account_id
    JOIN users u ON me.user_id = u.user_id
    LEFT JOIN accounts a ON t.related_account_id = a.account_id
    WHERE t.transaction_id = ? AND me.user_id = ?
");
$stmt->execute([$txnId, $userId]);
$txn = $stmt->fetch();

if (!$txn) {
    die('Transaction not found or access denied.');
}

// Create new PDF document
$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Nexus Bank');
$pdf->SetAuthor('Nexus Bank');
$pdf->SetTitle('Transaction Receipt');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(20, 20, 20);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 20);

// Add a page
$pdf->AddPage();

// Set colors
$pdf->SetDrawColor(70, 110, 255); // #706EFF - Nexus Bank blue
$pdf->SetTextColor(52, 60, 106);  // #343C6A - Dark blue

// Set font to support peso sign
$pdf->SetFont('dejavusans', '', 12, '', true);

// Add logo (if you have one)
// $pdf->Image('../assets/images/Logo-color.png', 20, 20, 40);

// Add decorative line
$pdf->SetLineWidth(0.5);
$pdf->Line(20, 35, 190, 35);

// Title
$pdf->Ln(25);
$pdf->SetFont('dejavusans', 'B', 28);
$pdf->Cell(0, 15, 'NEXUS BANK', 0, 1, 'C');
$pdf->SetFont('dejavusans', '', 16);
$pdf->Cell(0, 10, 'Transaction Receipt', 0, 1, 'C');

// Add decorative line
$pdf->Line(20, 60, 190, 60);
$pdf->Ln(15);

// Transaction Info Box
$pdf->SetFillColor(245, 245, 250); // Light gray background
$pdf->RoundedRect(20, 75, 170, 120, 5.00, '1111', 'DF');

// Transaction Info
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(30, 85);
$pdf->Cell(40, 8, 'Transaction ID:', 0, 0);
$pdf->SetFont('dejavusans', '', 12);
$pdf->SetX(75); // Explicitly set X for value
$pdf->Cell(0, 8, $txn['transaction_id'], 0, 1);

$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(30, 95);
$pdf->Cell(40, 8, 'Date & Time:', 0, 0);
$pdf->SetFont('dejavusans', '', 12);
$pdf->SetX(75); // Explicitly set X for value
$pdf->Cell(0, 8, date('j M Y, g:i A', strtotime($txn['created_at'])), 0, 1);

$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(30, 105);
$pdf->Cell(40, 8, 'Type:', 0, 0);
$pdf->SetFont('dejavusans', '', 12);
$pdf->SetX(75); // Explicitly set X for value
$pdf->Cell(0, 8, ucfirst(str_replace('_',' ',$txn['type'])), 0, 1);

$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(30, 115);
$pdf->Cell(40, 8, 'Amount:', 0, 0);
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->SetX(75); // Explicitly set X for value
$amount = $txn['amount'] ?? 0;
$formattedAmount = '₱' . number_format($amount, 2);
if (in_array($txn['type'], ['deposit','transfer_in'])) {
    $formattedAmount = '+' . $formattedAmount;
} elseif (in_array($txn['type'], ['withdrawal','transfer_out'])) {
    $formattedAmount = '-' . $formattedAmount;
}
$pdf->Cell(0, 8, $formattedAmount, 0, 1);

$pdf->SetFont('dejavusans', 'B', 12);
$pdf->SetXY(30, 125);
$pdf->Cell(40, 8, 'Account #:', 0, 0);
$pdf->SetFont('dejavusans', '', 12);
$pdf->SetX(75); // Explicitly set X for value
$pdf->Cell(0, 8, $txn['account_id'], 0, 1);

if ($txn['related_account_number']) {
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->SetXY(30, 135);
    $pdf->Cell(40, 8, 'Related Acct #:', 0, 0);
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->SetX(75); // Explicitly set X for value
    $pdf->Cell(0, 8, $txn['related_account_number'], 0, 1);
}

$pdf->Ln(15);

// Optional: branch by type for extra details
if ($txn['type'] === 'transfer_out') {
    $pdf->SetFont('dejavusans', 'I', 11);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->MultiCell(0, 6, 'Note: This was a transfer to another account. Make sure the recipient has acknowledged receipt.');
    $pdf->SetTextColor(52, 60, 106);
}

// Footer with user name and balance
$pdf->Ln(15);
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 8, "Account Holder: {$txn['full_name']}", 0, 1);

// Only show balance for deposits
if ($txn['type'] === 'deposit') {
    $balance = $txn['balance'] ?? 0;
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->Cell(0, 8, "Current Balance: ₱" . number_format($balance, 2), 0, 1);
}

// Add footer line
$pdf->Ln(15);
$pdf->SetLineWidth(0.5);
$pdf->Line(20, 260, 190, 260);

// Add footer text
$pdf->SetFont('dejavusans', 'I', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetXY(20, 265);
$pdf->Cell(0, 5, 'This is a computer-generated receipt. No signature is required.', 0, 1, 'C');
$pdf->SetXY(20, 270);
$pdf->Cell(0, 5, 'Thank you for banking with Nexus Bank', 0, 1, 'C');

// Add QR Code or Barcode (optional)
// $pdf->Image('path_to_qr_code.png', 20, 280, 30);

// Close and output PDF document
$filename = "receipt_{$txn['transaction_id']}.pdf";
$pdf->Output($filename, 'I'); // 'I' for inline display in browser
exit();
