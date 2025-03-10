<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['User_ID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Handle resume upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $resume_name = $_FILES['resume']['name'];
        $resume_tmp = $_FILES['resume']['tmp_name'];
        move_uploaded_file($resume_tmp, "path_to_resumes/$resume_name");
    } else {
        $resume_name = null;
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture_name = $_FILES['profile_picture']['name'];
        $profile_picture_tmp = $_FILES['profile_picture']['tmp_name'];
        move_uploaded_file($profile_picture_tmp, "uploaded_profile_pics/$profile_picture_name");
    } else {
        $profile_picture_name = null;
    }

    // Update user information in the database
    $update_query = "UPDATE users SET Username = ?, Email = ?, Resume = COALESCE(?, Resume), Profile_Picture = COALESCE(?, Profile_Picture) WHERE User_ID = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param("sssii", $username, $email, $resume_name, $profile_picture_name, $user_id);
    if ($stmt->execute()) {
        $_SESSION['update_success'] = "Information updated successfully.";
    } else {
        $_SESSION['update_error'] = "Failed to update information.";
    }
    $stmt->close();
    $con->close();

    header("Location: employee_dashboard.php");
    exit;
}
?>
