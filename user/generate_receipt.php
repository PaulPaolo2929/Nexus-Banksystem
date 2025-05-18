<?php
// generate_receipt.php

// Show errors for debugging; disable in production
ini_set('display_errors',1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../vendor/autoload.php';  // if using Composer

use FPDF;

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
    SELECT t.*, a.account_number AS related_account_number, u.full_name, a.balance
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

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica','B',16);

// Title
$pdf->Cell(0,10, 'SecureBank Transaction Receipt', 0,1,'C');

// Transaction Info
$pdf->SetFont('Helvetica','',12);
$pdf->Ln(5);
$pdf->Cell(50,8, 'Transaction ID:', 0,0);
$pdf->Cell(0,8, $txn['transaction_id'], 0,1);

$pdf->Cell(50,8, 'Date & Time:', 0,0);
$pdf->Cell(0,8, date('j M Y, g:i A', strtotime($txn['created_at'])), 0,1);

$pdf->Cell(50,8, 'Type:', 0,0);
$pdf->Cell(0,8, ucfirst(str_replace('_',' ',$txn['type'])), 0,1);

$pdf->Cell(50,8, 'Amount:', 0,0);
$sign = in_array($txn['type'], ['deposit','transfer_in']) ? '+' : '−';
$pdf->Cell(0,8, $sign . '$' . number_format($txn['amount'],2), 0,1);

$pdf->Cell(50,8, 'Your Account #:', 0,0);
$pdf->Cell(0,8, $txn['account_id'], 0,1);

$pdf->Cell(50,8, 'Related Acct #:', 0,0);
$pdf->Cell(0,8, $txn['related_account_number'] ?: 'N/A', 0,1);

// Optional: branch by type for extra details
if ($txn['type'] === 'transfer_out') {
    $pdf->Ln(5);
    $pdf->SetFont('Helvetica','I',11);
    $pdf->MultiCell(0,6, 'Note: This was a transfer to another account. Make sure the recipient has acknowledged receipt.');
}

// Footer with user name and remaining balance
$pdf->Ln(10);
$pdf->SetFont('Helvetica','',10);
$pdf->Cell(0,6, "Account Holder: {$txn['full_name']}", 0,1);
$pdf->Cell(0,6, "Remaining Balance: $" . number_format($txn['balance'],2), 0,1);

// Send headers to force download
$filename = "receipt_{$txn['transaction_id']}.pdf";
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="'. $filename .'"');
$pdf->Output('D', $filename);
exit();
