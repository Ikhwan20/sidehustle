<?php
session_start();
require_once 'config.php'; // Include your configuration file

// Validate session or authentication
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

include("connection.php");

// Example of updating application status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $applicationId = $_POST['application_id']; // Example: get application ID from form
    $newStatus = $_POST['new_status']; // Example: get new status from form

    // Update status in the database
    $updateQuery = "UPDATE job_applications SET Status = '$newStatus' WHERE Application_ID = $applicationId";
    if (mysqli_query($con, $updateQuery)) {
        // Send notification email to user
        require_once 'send_email.php'; // Include the file for sending emails

        // Optional: Redirect or output success message
        echo 'Application status updated successfully.';
    } else {
        echo 'Error updating application status: ' . mysqli_error($con);
    }
}

mysqli_close($con);
?>