<?php
// generate_receipt.php

// Increase memory limit for image processing
ini_set('memory_limit', '256M');

// Show errors for debugging; disable in production
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../vendor/autoload.php';  // TCPDF

// Make sure user is logged in
redirectIfNotLoggedIn();

// ---------------------------------------------------------------------------
// Helper to mask an account number (e.g. "123400150015" → "**** **** 0015")
// ---------------------------------------------------------------------------
function maskAccountNumber($acctNumber) {
    $acctNumber = trim($acctNumber);
    $len = strlen($acctNumber);
    if ($len <= 4) {
        return $acctNumber;
    }
    $last4 = substr($acctNumber, -4);
    return '**** **** ' . $last4;
}

// ---------------------------------------------------------------------------
// 1) Get transaction ID from query string
// ---------------------------------------------------------------------------
$txnId = $_GET['transaction_id'] ?? '';
if (!$txnId) {
    die('No transaction specified.');
}

// ---------------------------------------------------------------------------
// 2) Fetch transaction details from the DB (verifying it belongs to this user)
// ---------------------------------------------------------------------------
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        t.transaction_id,
        t.created_at,
        t.type,
        t.amount,
        t.description,
        
        -- The user's own account (for masking & new balance)
        me.account_number      AS account_number,
        me.balance             AS new_balance,
        
        -- Account holder's name
        u.full_name            AS full_name,

        -- Related account (if any)
        a.account_number       AS related_account_number,
        
        -- Loan due date if available (join with loans table)
        l.due_date             AS loan_due_date
    FROM transactions t
    JOIN accounts me       ON t.account_id = me.account_id
    JOIN users u           ON me.user_id = u.user_id
    LEFT JOIN accounts a   ON t.related_account_id = a.account_id
    LEFT JOIN loans l      ON t.type = 'approved_loan' AND l.amount = t.amount AND l.user_id = me.user_id
    WHERE t.transaction_id = ?
      AND me.user_id = ?
");
$stmt->execute([$txnId, $userId]);
$txn = $stmt->fetch();

if (!$txn) {
    die('Transaction not found or access denied.');
}

// ---------------------------------------------------------------------------
// 3) Derive values for display
// ---------------------------------------------------------------------------
$rawAmount  = floatval($txn['amount'] ?? 0);
$isCredit   = in_array($txn['type'], ['deposit','transfer_in', 'approved_loan', 'withdrawal_matured_investment']);
$signPrefix = $isCredit ? '+' : '-';
$absAmt     = number_format(abs($rawAmount), 2);
$displayAmt = $signPrefix . '₱ ' . $absAmt;

// Status is always “SUCCESS” for a completed transaction
$statusText   = 'SUCCESS';

// Format date as "10 December, 2025 20:22"
$formattedDate = date('j F, Y H:i', strtotime($txn['created_at']));

// Mask the user's own account number
$maskedAcct = maskAccountNumber($txn['account_number']);

// Related account (or "—" if none)
$relatedAcct = $txn['related_account_number']
    ? $txn['related_account_number']
    : '—';

// Currency is always "PHP"
$currency = 'PHP';

// ---------------------------------------------------------------------------
// 4) Create TCPDF document
// ---------------------------------------------------------------------------
$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Document metadata
$pdf->SetCreator('Nexus Bank');
$pdf->SetAuthor('Nexus Bank');
$pdf->SetTitle('Transaction Receipt');

// Disable default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Margins: left=20mm, top=20mm, right=20mm
$pdf->SetMargins(20, 20, 20);

// Auto page breaks with bottom margin = 20mm
$pdf->SetAutoPageBreak(TRUE, 20);

// Add first page
$pdf->AddPage();

// ────────────────────────────────────────────────────────────────────────────
// A) COLORS & FONT SHORTCUTS
// ────────────────────────────────────────────────────────────────────────────
$blueBright    = [0, 174, 239];   // #00AEEF
$darkBlue      = [52,  60,  106]; // #343C6A
$greyMedium    = [128, 128, 128]; // #808080
$greyLight     = [200, 200, 200]; // #C8C8C8
$greyPromo     = [150, 150, 150]; // #969696

// All text will use DejaVu Sans (UTF-8 support)
$fontNormal    = 'dejavusans';
$fontBold      = 'dejavusans';
$fontItalic    = 'dejavusans';

// ────────────────────────────────────────────────────────────────────────────
// B) HEADER: Logo + "NEXUS E-BANKING SYSTEM" (left)     "Transaction Receipt" (right)
// ────────────────────────────────────────────────────────────────────────────

// B.1) Nexus Logo at (20mm, 20mm), width = 30mm
$logoFile = '../assets/images/LOGO-RECEIPT.jpg';
if (file_exists($logoFile)) {
    // Arguments: file, x, y, width (mm), height (mm), type="", link="", align="", resize=false, dpi=300
    $pdf->Image($logoFile, 20, 20, 30, 0, '', '', 'T', false, 300);
}

// B.4) "Transaction Receipt" (12pt bold, dark blue) on the top‐right
$pdf->SetTextColor(...$darkBlue);
$pdf->SetFont($fontBold, 'B', 12);
$pdf->SetXY(20, 20);
$pdf->Cell(0, 6, 'Transaction Receipt', 0, 1, 'R', false);

// ────────────────────────────────────────────────────────────────────────────
// C) LARGE AMOUNT + STATUS + DATE
// ────────────────────────────────────────────────────────────────────────────

// C.1) Move down to about Y = 50mm
$pdf->Ln(25);

// Big, blue amount (centered)
if (in_array($txn['type'], ['withdrawal', 'transfer_out', 'investment'])) {
    $amountColor = [255, 0, 0]; // red
} else {
    $amountColor = [0, 174, 239]; // Nexus blue
}

// C.2) Amount (32pt bold, colored), centered
$pdf->SetTextColor(...$amountColor);
$pdf->SetFont($fontBold, 'B', 32);
$pdf->Cell(0, 12, $displayAmt, 0, 1, 'C', false);

// C.3) "SUCCESS" (18pt bold, dark blue), centered
$pdf->SetTextColor(...$darkBlue);
$pdf->SetFont($fontBold, 'B', 18);
$pdf->Cell(0, 10, $statusText, 0, 1, 'C', false);

// C.4) Date (12pt normal, medium grey), centered
$pdf->SetTextColor(...$greyMedium);
$pdf->SetFont($fontNormal, '', 12);
$pdf->Cell(0, 8, $formattedDate, 0, 1, 'C', false);

// ────────────────────────────────────────────────────────────────────────────
// D) First Divider Line (1pt thick, light grey) at current Y
// ────────────────────────────────────────────────────────────────────────────
$pdf->Ln(2);
$pdf->SetDrawColor(...$greyLight);
$pdf->SetLineWidth(0.5);
$yDivider1 = $pdf->GetY();
$pdf->Line(20, $yDivider1, 190, $yDivider1);
$pdf->Ln(8);

// ────────────────────────────────────────────────────────────────────────────
// E) TRANSACTION DETAILS TABLE (Left labels, colon, right‐aligned values)
// ────────────────────────────────────────────────────────────────────────────

// E.1) Prepare all rows as [label, value]
$rows = [
    ['Transaction ID',       $txn['transaction_id']],
    ['Account Number',       $maskedAcct],
    ['Account Holder',       $txn['full_name']],
    ['Transaction Type',     strtoupper($txn['type'])],
    ['Currency',             $currency],
    ['New Account Balance',  number_format($txn['new_balance'], 2)],
    ['Related Account',      $relatedAcct],
];

// E.2) Layout constants
$xLabel     = 20;   // left margin for label
$wLabel     = 60;   // width for label cell
$xColon     = $xLabel + $wLabel + 2;  // small gap, colon
$wColon     = 5;    // width for colon
$xValue     = $xColon + $wColon + 2;  // start of value cell
$wValue     = 190 - $xValue;  // until right margin (190mm)
$rowHeight  = 10;   // each row is 10mm tall

// E.3) Loop through rows
foreach ($rows as $row) {
    list($label, $value) = $row;

    // Label (11pt normal, medium grey)
    $pdf->SetTextColor(...$greyMedium);
    $pdf->SetFont($fontNormal, '', 11);
    $pdf->SetXY($xLabel, $pdf->GetY());
    $pdf->Cell($wLabel, $rowHeight, $label, 0, 0, 'L', false);

    // Colon ":" (11pt normal, medium grey)
    $pdf->SetTextColor(...$greyMedium);
    $pdf->SetFont($fontNormal, '', 11);
    $pdf->SetXY($xColon, $pdf->GetY());
    $pdf->Cell($wColon, $rowHeight, ':', 0, 0, 'L', false);

    // Value (11pt bold, dark blue), right‐aligned
    $pdf->SetTextColor(...$darkBlue);
    $pdf->SetFont($fontBold, 'B', 11);
    $pdf->SetXY($xValue, $pdf->GetY());
    $pdf->Cell($wValue, $rowHeight, $value, 0, 1, 'R', false);
}

// ────────────────────────────────────────────────────────────────────────────
// F) Second Divider (1pt thick, light grey) after last detail row
// ────────────────────────────────────────────────────────────────────────────
$pdf->Ln(2);
$pdf->SetDrawColor(...$greyLight);
$pdf->SetLineWidth(0.5);
$yDivider2 = $pdf->GetY();
$pdf->Line(20, $yDivider2, 190, $yDivider2);
$pdf->Ln(8);

// ────────────────────────────────────────────────────────────────────────────
// G) DESCRIPTION ROW (Same style, single row)
// ────────────────────────────────────────────────────────────────────────────

// G.1) Label = "Description"
$pdf->SetTextColor(...$greyMedium);
$pdf->SetFont($fontNormal, '', 11);
$pdf->SetXY($xLabel, $pdf->GetY());
$pdf->Cell($wLabel, $rowHeight, 'Description', 0, 0, 'L', false);

// G.2) Colon ":"
$pdf->SetTextColor(...$greyMedium);
$pdf->SetFont($fontNormal, '', 11);
$pdf->SetXY($xColon, $pdf->GetY());
$pdf->Cell($wColon, $rowHeight, ':', 0, 0, 'L', false);

$descText = trim($txn['description'] ?? '');
if ($descText === '') {
    $descText = '—';
}
$pdf->SetTextColor(...$darkBlue);
$pdf->SetFont($fontBold, 'B', 11);
$pdf->SetXY($xValue, $pdf->GetY());
$pdf->Cell($wValue, $rowHeight, $descText, 0, 1, 'R', false);

// Add Due Date row for approved loans
if ($txn['type'] === 'approved_loan' && !empty($txn['loan_due_date'])) {
    $pdf->SetTextColor(...$greyMedium);
    $pdf->SetFont($fontNormal, '', 11);
    $pdf->SetXY($xLabel, $pdf->GetY());
    $pdf->Cell($wLabel, $rowHeight, 'Due Date', 0, 0, 'L', false);

    $pdf->SetTextColor(...$greyMedium);
    $pdf->SetFont($fontNormal, '', 11);
    $pdf->SetXY($xColon, $pdf->GetY());
    $pdf->Cell($wColon, $rowHeight, ':', 0, 0, 'L', false);

    $dueDateFormatted = date('M j, Y', strtotime($txn['loan_due_date']));
    $pdf->SetTextColor(...$darkBlue);
    $pdf->SetFont($fontBold, 'B', 11);
    $pdf->SetXY($xValue, $pdf->GetY());
    $pdf->Cell($wValue, $rowHeight, $dueDateFormatted, 0, 1, 'R', false);
}

// ────────────────────────────────────────────────────────────────────────────
// H) Third Divider (1pt thick, light grey) after description
// ────────────────────────────────────────────────────────────────────────────
$pdf->Ln(2);
$pdf->SetDrawColor(...$greyLight);
$pdf->SetLineWidth(0.5);
$yDivider3 = $pdf->GetY();
$pdf->Line(20, $yDivider3, 190, $yDivider3);
$pdf->Ln(12);

// ────────────────────────────────────────────────────────────────────────────
// I) FOOTER TEXT ("system‐generated receipt…" + Support + email)
// ────────────────────────────────────────────────────────────────────────────

// I.1) "This is a system‐generated receipt. No signature is required." (11pt normal, grey)
// We want two lines, centered.
$pdf->SetTextColor(...$greyMedium);
$pdf->SetFont($fontNormal, '', 11);
// Use MultiCell to handle the line break:
$pdf->MultiCell(
    0,               // width = full content width
    6,               // line height
    "This is a system-generated receipt.\nNo signature is required.",
    0,               // no border
    'C',             // centered
    false,           // fill = false
    1                // move cursor to next line
);
$pdf->Ln(6);

// I.2) "Support" (12pt normal, dark blue), centered
$pdf->SetTextColor(...$darkBlue);
$pdf->SetFont($fontNormal, '', 12);
$pdf->Cell(0, 6, 'Support', 0, 1, 'C', false);

// I.3) Support email (12pt bold, bright blue), centered
$pdf->SetTextColor(...$blueBright);
$pdf->SetFont($fontBold, 'B', 12);
$pdf->Cell(0, 6, 'nexusbanksystem@gmail.com', 0, 1, 'C', false);
$pdf->Ln(8);

// ────────────────────────────────────────────────────────────────────────────
// J) DOTTED GREY LINE (0.5pt, dash pattern 2/2 for better visibility)

// J.1) Set dash pattern for dotted line (2mm on, 2mm off)
$pdf->SetDrawColor(...$greyLight);
$pdf->SetLineWidth(0.5);
$pdf->SetLineStyle(['width' => 0.5, 'dash' => '2,2', 'color' => $greyLight]);

// J.2) Draw the line from x=20 to x=190 at current Y
$yDotted = $pdf->GetY();
$pdf->Line(20, $yDotted, 190, $yDotted);

// J.3) Reset to solid lines
$pdf->SetLineStyle(['width' => 0.5, 'dash' => 0, 'color' => $greyLight]);
$pdf->Ln(10);

// ────────────────────────────────────────────────────────────────────────────
// K) PROMO / DISCLAIMER PARAGRAPH (9pt normal, light grey), centered
// ────────────────────────────────────────────────────────────────────────────
$pdf->SetTextColor(...$greyPromo);
$pdf->SetFont($fontNormal, '', 9);

// We manually insert line breaks to avoid automatic wrapping outside margins.
$promoText = 
    "Enjoy a better life with Nexus Bank. Get free transfers, withdrawals, bill payments, instant\n" .
    "loans, and competitive annual interest on your savings. Nexus Bank is licensed by the\n" .
    "Bangko Sentral ng Pilipinas and insured by the PDIC.";

$pdf->MultiCell(
    0,            // full width
    5,            // line height
    $promoText,
    0,            // no border
    'C',          // centered
    false
);

// ────────────────────────────────────────────────────────────────────────────
// L) OUTPUT THE PDF
// ────────────────────────────────────────────────────────────────────────────
$filename = "receipt_{$txn['transaction_id']}.pdf";
$pdf->Output($filename, 'I');
exit();
