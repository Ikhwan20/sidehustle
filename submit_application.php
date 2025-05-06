<?php
// Turn off output buffering and clear any previous output
if (ob_get_level()) {
    ob_end_clean();
}
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

// Check if user is logged in
if (!isset($_SESSION['User_ID'])) {
    outputJsonAndExit(['status' => 'error', 'message' => 'User not logged in.']);
}

$user_id = $_SESSION['User_ID'];

// Validate job ID
if (!isset($_POST['job-id']) || empty($_POST['job-id'])) {
    outputJsonAndExit(['status' => 'error', 'message' => 'Job ID not provided.']);
}

// Sanitize inputs
$job_id = filter_var($_POST['job-id'], FILTER_SANITIZE_NUMBER_INT);
$name = filter_var($_POST['full-name'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

// Fetch job title
$job_query = "SELECT Title FROM jobs WHERE Job_ID = ?";
$job_stmt = $con->prepare($job_query);
$job_stmt->bind_param("i", $job_id);
$job_stmt->execute();
$job_result = $job_stmt->get_result();

if ($job_result->num_rows === 0) {
    outputJsonAndExit(['status' => 'error', 'message' => 'Invalid job ID.']);
}

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
$check_stmt->close();

// Determine which resume to use
$resume_path = '';

// If using existing resume
if (isset($_POST['use_existing_resume']) && $_POST['use_existing_resume'] == '1' && 
    isset($_POST['existing_resume_path']) && !empty($_POST['existing_resume_path'])) {
    
    $resume_path = $_POST['existing_resume_path'];
    
    // Verify the resume file exists
    if (!file_exists($resume_path)) {
        outputJsonAndExit(['status' => 'error', 'message' => 'Existing resume file not found.']);
    }
} 
// If uploading a new resume
else if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    // Validate file type
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['resume']['tmp_name']);
    finfo_close($file_info);
    
    if (!in_array($mime_type, $allowed_types)) {
        outputJsonAndExit(['status' => 'error', 'message' => 'Invalid file type. Please upload PDF, DOC, or DOCX.']);
    }
    
    // Create safe filename
    $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['resume']['name']));
    $target_dir = "uploads/";
    $resume_path = $target_dir . $filename;
    
    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Upload file
    if (!move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
        outputJsonAndExit(['status' => 'error', 'message' => 'Error uploading resume.']);
    }
} else {
    outputJsonAndExit(['status' => 'error', 'message' => 'Resume file is required.']);
}

// Insert application into database
$insert_query = "INSERT INTO job_applications (Job_ID, User_ID, Resume, Applied_At) VALUES (?, ?, ?, NOW())";
$insert_stmt = $con->prepare($insert_query);
$insert_stmt->bind_param("iis", $job_id, $user_id, $resume_path);

if ($insert_stmt->execute()) {
    // Log success to a file instead of echoing
    error_log("Application submitted successfully for user: $user_id, job: $job_id");
    
    // Try to send emails silently (before outputting JSON response)
    try {
        $admin_email = "meiyun.nmy@gmail.com";
        $applicant_subject = "Application Submitted for $job_title";
        $applicant_body = "Dear $name,\n\nThank you for applying for the position of $job_title. We have received your application and will review it shortly.\n\nBest regards,\nJob Portal Team";
        $admin_subject = "New Application for $job_title";
        $admin_body = "Dear Admin,\n\nUser $name ($email) has applied for the position of $job_title.\n\nBest regards,\nJob Portal System";

        sendEmailSilently($email, $applicant_subject, $applicant_body, $resume_path);
        sendEmailSilently($admin_email, $admin_subject, $admin_body, $resume_path);
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        // Continue anyway - don't break the JSON response
    }
    
    outputJsonAndExit(['status' => 'success', 'message' => 'Application submitted successfully.']);
} else {
    outputJsonAndExit(['status' => 'error', 'message' => 'Error applying for the job: ' . $con->error]);
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
        $mail->Password   = 'tuvz reht znpj hgdk'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Content
        $mail->setFrom('ngmeiyun1@gmail.com', 'Side Hustle');
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