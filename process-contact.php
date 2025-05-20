<?php 
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/includes/mailer.php';
require_once 'includes/db.php';

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);

    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }

    // If no errors, proceed with sending email
    if (empty($errors)) {
        try {
            // Create a new PHPMailer instance
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'shaison62@gmail.com';
            $mail->Password   = 'awxn tpnn ogsm grut';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('shaison62@gmail.com', 'Nexus Bank');
            $mail->addAddress('shaison62@gmail.com'); // Your receiving email
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Contact Form: " . $subject;
            
            // Create HTML email body
            $htmlBody = "
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong></p>
                <p>" . nl2br($message) . "</p>
            ";
            
            $mail->Body = $htmlBody;
            $mail->AltBody = "Name: {$name}\nEmail: {$email}\nSubject: {$subject}\n\nMessage:\n{$message}";

            // Send email
            $mail->send();

            // Store contact message in database
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $subject, $message]);
                
                $_SESSION['contact_success'] = "Thank you for your message! We will get back to you soon.";
            } catch (PDOException $e) {
                error_log("Error storing contact message: " . $e->getMessage());
                $_SESSION['contact_success'] = "Thank you for your message! We will get back to you soon.";
            }
        } catch (Exception $e) {
            error_log("Email sending failed: " . $mail->ErrorInfo);
            $_SESSION['contact_error'] = "Sorry, there was an error sending your message. Please try again later.";
        }
    } else {
        $_SESSION['contact_error'] = implode("<br>", $errors);
    }

    // Redirect back to contact page
    header("Location: contact.php");
    exit();
} else {
    // Invalid request method
    header('Location: contact.php');
    exit();
}
?>
