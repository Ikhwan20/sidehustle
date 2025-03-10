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
        // Move uploaded file to desired directory
        $upload_directory = 'path_to_resumes/'; // Adjust this to your directory
        $new_resume_path = $upload_directory . $file_name;

        if (move_uploaded_file($file_tmp, $new_resume_path)) {
            // Update database with new resume path
            $update_query = "UPDATE users SET Resume = ? WHERE User_ID = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("si", $new_resume_path, $user_id);
            if ($update_stmt->execute()) {
                // Resume updated successfully
                echo "Resume updated successfully.";
            } else {
                echo "Failed to update resume.";
            }
            $update_stmt->close();
        } else {
            echo "Error moving file to upload directory.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}

$con->close();
?>
