<?php
// Turn off output buffering and clear any previous output
ob_end_clean();
// Start fresh buffer
ob_start();

session_start();

require 'vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("connection.php");

// Turn off any error output
error_reporting(0);
ini_set('display_errors', 0);

// Store any errors to handle later
$errors = [];

if (!isset($_SESSION['User_ID'])) {
    outputJsonAndExit(['status' => 'error', 'message' => 'User not logged in.']);
}

$user_id = $_SESSION['User_ID'];

if (!isset($_POST['job-id']) || empty($_POST['job-id'])) {
    outputJsonAndExit(['status' => 'error', 'message' => 'Job ID not provided.']);
}

$job_id = $_POST['job-id'];
$name = $_POST['full-name'];
$email = $_POST['email'];
$resume = $_FILES['resume']['name'];
$resume_temp = $_FILES['resume']['tmp_name'];

// Fetch job title
$job_query = "SELECT Title FROM jobs WHERE Job_ID = ?";
$job_stmt = $con->prepare($job_query);
$job_stmt->bind_param("i", $job_id);
$job_stmt->execute();
$job_result = $job_stmt->get_result();
$job = $job_result->fetch_assoc();
$job_title = $job['Title'];
$job_stmt->close();

// Check if the user has already applied for this job
$check_query = "SELECT * FROM job_applications WHERE Job_ID = ? AND User_ID = ?";
$check_stmt = $con->prepare($check_query);
$check_stmt->bind_param("ii", $job_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    outputJsonAndExit(['status' => 'error', 'message' => 'You have already applied for this job.']);
}

// Upload resume
$target_dir = "uploads/";
$target_file = $target_dir . basename($resume);

if (!move_uploaded_file($resume_temp, $target_file)) {
    outputJsonAndExit(['status' => 'error', 'message' => 'Error uploading resume.']);
}

// Insert application into database
$insert_query = "INSERT INTO job_applications (Job_ID, User_ID, Resume, Applied_At) VALUES (?, ?, ?, NOW())";
$insert_stmt = $con->prepare($insert_query);
$insert_stmt->bind_param("iis", $job_id, $user_id, $target_file);

if ($insert_stmt->execute()) {
    // Log success to a file instead of echoing
    error_log("Application submitted successfully for user: $user_id, job: $job_id");
    
    // Try to send emails silently (don't output anything)
    try {
        $admin_email = "meiyun.nmy@gmail.com";
        $applicant_subject = "Application Submitted for $job_title";
        $applicant_body = "Dear $name,\n\nThank you for applying for the position of $job_title. We have received your application and will review it shortly.\n\nBest regards,\nJob Portal Team";
        $admin_subject = "New Application for $job_title";
        $admin_body = "Dear Admin,\n\nUser $name ($email) has applied for the position of $job_title.\n\nBest regards,\nJob Portal System";

        sendEmailSilently($email, $applicant_subject, $applicant_body, $target_file);
        sendEmailSilently($admin_email, $admin_subject, $admin_body, $target_file);
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        // Continue anyway - don't break the JSON response
    }
    
    outputJsonAndExit(['status' => 'success', 'message' => 'Application submitted successfully.']);
} else {
    outputJsonAndExit(['status' => 'error', 'message' => 'Error applying for the job.']);
}

$insert_stmt->close();
$con->close();

function sendEmailSilently($to, $subject, $body, $attachment = null) {
    try {
        $mail = new PHPMailer(true);
        // Silent mode
        $mail->SMTPDebug = 0;
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ngmeiyun1@gmail.com';
        $mail->Password   = 'fmlv tzwd niqb wnnm'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Content
        $mail->setFrom('sidehustle@gmail.com', 'Job Portal');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Attach resume if provided
        if ($attachment) {
            $mail->addAttachment($attachment);
        }

        $mail->send();
        error_log("Email sent silently to $to");
        return true;
    } catch (Exception $e) {
        error_log("Silent Mailer Error: " . $e->getMessage());
        return false;
    }
}

function outputJsonAndExit($data) {
    // Clear any buffered output
    ob_end_clean();
    
    // Set proper content type
    header('Content-Type: application/json');
    
    // Output the JSON and exit
    echo json_encode($data);
    exit;
}
?>