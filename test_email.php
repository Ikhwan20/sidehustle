<?php
$to = "puterifiqrina02@gmail.com";
$subject = "Test mail";
$message = "This is a test email.";
$headers = "From: webmaster@example.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail sent successfully.";
} else {
    echo "Mail sending failed.";
    error_log("Failed to send email to $to");
    error_log("Error Details: " . print_r(error_get_last(), true));
}
?>
