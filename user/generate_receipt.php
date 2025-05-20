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
$pdf->SetMargins(15, 15, 15);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// Add a page
$pdf->AddPage();

// Set colors
$pdf->SetDrawColor(70, 110, 255); // #706EFF - Nexus Bank blue
$pdf->SetTextColor(52, 60, 106);  // #343C6A - Dark blue

// Add logo (if you have one)
// $pdf->Image('../assets/images/Logo-color.png', 15, 15, 40);

// Add decorative line
$pdf->SetLineWidth(0.5);
$pdf->Line(15, 30, 195, 30);

// Title
$pdf->Ln(20);
$pdf->SetFont('helvetica', 'B', 24);
$pdf->Cell(0, 10, 'NEXUS BANK', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 14);
$pdf->Cell(0, 8, 'Transaction Receipt', 0, 1, 'C');

// Add decorative line
$pdf->Line(15, 55, 195, 55);
$pdf->Ln(10);

// Transaction Info Box
$pdf->SetFillColor(245, 245, 250); // Light gray background
$pdf->RoundedRect(15, 65, 180, 100, 3.50, '1111', 'DF');

// Transaction Info
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(25, 75);
$pdf->Cell(50, 8, 'Transaction ID:', 0, 0);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, $txn['transaction_id'], 0, 1);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(25, 85);
$pdf->Cell(50, 8, 'Date & Time:', 0, 0);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, date('j M Y, g:i A', strtotime($txn['created_at'])), 0, 1);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(25, 95);
$pdf->Cell(50, 8, 'Type:', 0, 0);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, ucfirst(str_replace('_',' ',$txn['type'])), 0, 1);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(25, 105);
$pdf->Cell(50, 8, 'Amount:', 0, 0);
$pdf->SetFont('helvetica', 'B', 12);
$amount = $txn['amount'] ?? 0;
$formattedAmount = '$' . number_format($amount, 2);
if (in_array($txn['type'], ['deposit','transfer_in'])) {
    $formattedAmount = '+' . $formattedAmount;
} elseif (in_array($txn['type'], ['withdrawal','transfer_out'])) {
    $formattedAmount = '-' . $formattedAmount;
}
$pdf->Cell(0, 8, $formattedAmount, 0, 1);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(25, 115);
$pdf->Cell(50, 8, 'Account #:', 0, 0);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, $txn['account_id'], 0, 1);

if ($txn['related_account_number']) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(25, 125);
    $pdf->Cell(50, 8, 'Related Acct #:', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, $txn['related_account_number'], 0, 1);
}

$pdf->Ln(10);

// Optional: branch by type for extra details
if ($txn['type'] === 'transfer_out') {
    $pdf->SetFont('helvetica', 'I', 11);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->MultiCell(0, 6, 'Note: This was a transfer to another account. Make sure the recipient has acknowledged receipt.');
    $pdf->SetTextColor(52, 60, 106);
}

// Footer with user name and balance
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, "Account Holder: {$txn['full_name']}", 0, 1);

// Only show balance for deposits
if ($txn['type'] === 'deposit') {
    $balance = $txn['balance'] ?? 0;
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Current Balance: $" . number_format($balance, 2), 0, 1);
}

// Add footer line
$pdf->Ln(10);
$pdf->SetLineWidth(0.5);
$pdf->Line(15, 270, 195, 270);

// Add footer text
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(100, 100, 100);
$pdf->SetXY(15, 275);
$pdf->Cell(0, 5, 'This is a computer-generated receipt. No signature is required.', 0, 1, 'C');
$pdf->SetXY(15, 280);
$pdf->Cell(0, 5, 'Thank you for banking with Nexus Bank', 0, 1, 'C');

// Close and output PDF document
$filename = "receipt_{$txn['transaction_id']}.pdf";
$pdf->Output($filename, 'I'); // 'I' for inline display in browser
exit();
