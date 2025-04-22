<?php
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/db.php';

function generateOTP($email) {
    global $pdo;
    
    // Normalize email to lowercase
    $email = strtolower(trim($email));
    
    // Generate 6-digit OTP with proper randomization
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    try {
        // Clear existing OTPs including expired ones
        $pdo->prepare("DELETE FROM otp_verification WHERE email = ?")
            ->execute([$email]);
        
        // Insert new OTP with UTC timestamp
        $stmt = $pdo->prepare("INSERT INTO otp_verification 
                            (email, otp_code, expires_at, is_used)
                            VALUES (?, ?, ?, 0)");
        $stmt->execute([$email, $otp, $expiresAt]);
        
        // Debug logging
        error_log("Generated OTP for $email: $otp (Expires: $expiresAt)");
        
        return sendOTP($email, $otp);
    } catch (PDOException $e) {
        error_log("OTP Generation Error: " . $e->getMessage());
        return false;
    }
}

function verifyOTP($email, $otp) {
    global $pdo;
    
    // Normalize inputs
    $email = strtolower(trim($email));
    $otp = trim($otp);
    
    // Validate OTP format first
    if (!preg_match('/^\d{6}$/', $otp)) {
        error_log("Invalid OTP format: $otp");
        return false;
    }

    try {
        // Use UTC time comparison and case-insensitive email match
        $stmt = $pdo->prepare("SELECT * FROM otp_verification 
                            WHERE LOWER(email) = ?
                            AND otp_code = ?
                            AND expires_at > UTC_TIMESTAMP()
                            AND is_used = 0");
        $stmt->execute([$email, $otp]);
        
        if ($stmt->fetch()) {
            // Immediately mark as used
            $pdo->prepare("UPDATE otp_verification SET is_used = 1 
                         WHERE LOWER(email) = ? AND otp_code = ?")
                ->execute([$email, $otp]);
            
            error_log("OTP verification successful for $email");
            return true;
        }
        
        error_log("OTP verification failed for $email");
        return false;
        
    } catch (PDOException $e) {
        error_log("OTP Verification Error: " . $e->getMessage());
        return false;
    }
}
?>