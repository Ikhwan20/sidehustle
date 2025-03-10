<?php
session_start();
// Connect to MySQL
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];
    
    // Email configuration
    $to = "puterifiqrina02@example.com"; // Replace with your email address
    $subject = "New Query Form Submission";
    $body = "Name: $name\nEmail: $email\nMessage:\n$message";
    $headers = "From: $email";
    
    // Send email
    if (mail($to, $subject, $body, $headers)) {
        echo "Message sent successfully!";
    } else {
        echo "Error: Unable to send message.";
    }
} else {
    echo "Error: Invalid request.";
}

$conn->close();
?>