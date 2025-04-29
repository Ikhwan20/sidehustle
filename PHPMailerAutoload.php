<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP; // Ensure SMTP class is included

require 'vendor/autoload.php'; // Adjust path as necessary

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ngmeiyun1@gmail.com'; // Your Gmail email address
    $mail->Password = 'tuvz reht znpj hgdk'; // Your Gmail password or app-specific password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
    $mail->Port = 587; // TCP port to connect to

    //Recipients
    $mail->setFrom('your_email@gmail.com', 'Your Name'); // Your email address and name
    $mail->addAddress('recipient@example.com', 'Recipient Name'); // Recipient's email address and name

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Subject';
    $mail->Body = 'Message body in HTML format';

    // Enable verbose debug output
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>