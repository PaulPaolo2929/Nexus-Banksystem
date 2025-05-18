<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendResetLink($recipientEmail, $resetLink) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'shaison62@gmail.com'; // Your Gmail
        $mail->Password   = 'awxn tpnn ogsm grut'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('shaison62@gmail.com', 'Nexus Bank Reset Password');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Nexus Bank Account Password';
        $mail->Body = "
            Dear User,<br><br>

            We received a request to reset the password associated with your account. If you made this request, please click the link below to set a new password:<br><br>

            <a href='$resetLink'>$resetLink</a><br><br>

            <strong>Important:</strong> This password reset link is valid for <strong>1 hour</strong> from the time it was generated. After this time, the link will expire for security purposes. If the link has expired, you will need to request a new one by returning to the password reset page.<br><br>

            <strong>Note:</strong> If you did not initiate this request, no further action is required. Your account remains secure, and you can safely ignore this email. However, if you are concerned about unauthorized access, we recommend reviewing your account activity and updating your password as a precaution.<br><br>

            Should you encounter any issues or need further assistance, please don't hesitate to contact our support team. We're here to help.<br><br>

            Thank you,<br>
            Nexus E-Banking System Support Team
        ";        
        $mail->AltBody = "Click the link to reset your password: $resetLink";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Reset Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
