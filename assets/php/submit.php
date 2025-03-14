<?php
// Disable error display to the user
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/your/error.log'); // Ensure this path is writable

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    // Verify reCAPTCHA
    $recaptchaSecret = '6Ldb6_EqAAAAAP_OadZ0K30E3kf303GMyn7jQeyc';
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $recaptcha = json_decode($recaptcha);

    if ($recaptcha && $recaptcha->success) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'redbreloom3@gmail.com';
            $mail->Password = 'lscbbpnnpinfmqur';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('daniel2000booth@gmail.com', 'Your Name');
            $mail->addAddress('redbreloom3@gmail.com', 'Recipient Name');
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br("Name: $name\nEmail: $email\n\n$message");
            $mail->AltBody = "Name: $name\nEmail: $email\n\n$message";

            $mail->send();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            echo json_encode(['success' => false, 'error' => 'Message could not be sent.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'reCAPTCHA verification failed.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}
?>