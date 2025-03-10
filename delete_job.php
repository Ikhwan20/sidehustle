<?php
session_start();
include("connection.php");
include("functions.php");

if(!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

if(isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    
    $query = "DELETE FROM jobs WHERE Job_ID='$job_id' AND Employer_ID='{$_SESSION['employer_id']}'";
    if(mysqli_query($con, $query)) {
        $_SESSION['success_message'] = "Job deleted successfully.";
    } else {
        $_SESSION['success_message'] = "Error deleting job.";
    }
}

header("Location: employer_dashboard.php");
die;
?>
