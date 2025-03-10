<?php
session_start();

// Check if the employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

// Include connection file
include("connection.php");

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_id = mysqli_real_escape_string($con, $_POST['application_id']);
    $new_status = mysqli_real_escape_string($con, $_POST['status']);

    // Update the status in the database
    $update_query = "UPDATE applications SET status = '$new_status' WHERE id = '$application_id'";
    if (mysqli_query($con, $update_query)) {
        $_SESSION['success_message'] = "Status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Error updating status: " . mysqli_error($con);
    }
    header("Location: view_applicants.php?job_id=" . $_POST['job_id']);
    die;
} else {
    header("Location: employer_dashboard.php");
    die;
}
?>