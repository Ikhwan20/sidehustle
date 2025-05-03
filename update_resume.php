<?php
session_start();
include("connection.php");

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {
    $file_name = $_FILES['resume']['name'];
    $file_tmp = $_FILES['resume']['tmp_name'];
    $file_error = $_FILES['resume']['error'];

    if ($file_error === UPLOAD_ERR_OK) {
        $upload_directory = 'uploads/';
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_name = 'resume_' . $user_id . '_' . time() . '.' . $file_ext;
        $new_resume_path = $upload_directory . $unique_name;

        // Optional: delete old resume file
        $stmt = $con->prepare("SELECT Resume FROM users WHERE User_ID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($old_resume);
        if ($stmt->fetch() && !empty($old_resume) && file_exists($old_resume)) {
            unlink($old_resume);
        }
        $stmt->close();

        if (move_uploaded_file($file_tmp, $new_resume_path)) {
            $update_query = "UPDATE users SET Resume = ? WHERE User_ID = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("si", $new_resume_path, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }
}

$con->close();
header("Location: dashboard.php");
exit;
