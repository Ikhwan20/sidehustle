<?php
session_start();

require 'vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("connection.php");

if (!isset($_SESSION['User_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$user_id = $_SESSION['User_ID'];

if (!isset($_POST['job-id']) || empty($_POST['job-id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Job ID not provided.']);
    exit;
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
    echo json_encode(['status' => 'error', 'message' => 'You have already applied for this job.']);
    exit;
}

// Upload resume
$target_dir = "uploads/";
$target_file = $target_dir . basename($resume);

if (!move_uploaded_file($resume_temp, $target_file)) {
    echo json_encode(['status' => 'error', 'message' => 'Error uploading resume.']);
    exit;
}

// Insert application into database
$insert_query = "INSERT INTO job_applications (Job_ID, User_ID, Resume, Applied_At) VALUES (?, ?, ?, NOW())";
$insert_stmt = $con->prepare($insert_query);
$insert_stmt->bind_param("iis", $job_id, $user_id, $target_file);

if ($insert_stmt->execute()) {
    // Send email notifications
    $admin_email = "meiyun.nmy@gmail.com";
    // Email to applicant
    $applicant_subject = "Application Submitted for $job_title";
    $applicant_body = "Dear $name,\n\nThank you for applying for the position of $job_title. We have received your application and will review it shortly.\n\nBest regards,\nJob Portal Team";

    // Email to admin
    $admin_subject = "New Application for $job_title";
    $admin_body = "Dear Admin,\n\nUser $name ($email) has applied for the position of $job_title.\n\nBest regards,\nJob Portal System";

    sendEmail($email, $applicant_subject, $applicant_body, $target_file);
    sendEmail($admin_email, $admin_subject, $admin_body, $target_file);

    echo json_encode(['status' => 'success', 'message' => 'Application submitted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error applying for the job.']);
}

$insert_stmt->close();
$con->close();

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Enable debugging
        $mail->SMTPDebug = 2; // Set to 3 for more details
        $mail->Debugoutput = 'html';
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ikhwanmazlan20@gmail.com';
        $mail->Password   = 'lfxr gnpd fazn mzuf'; 
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
        echo "Email sent successfully to $to <br>";
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo . "<br>";
        error_log("Email sending failed: " . $mail->ErrorInfo);
    }
}
?>
