<?php
session_start();
include("connection.php");

// Check if the user is logged in
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];

// Check if a file was uploaded
if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    // Get the uploaded file information
    $file_name = $_FILES['resume']['name']; // Original filename
    $file_tmp = $_FILES['resume']['tmp_name'];
    $file_size = $_FILES['resume']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Check file extension
    $allowed_extensions = array('pdf', 'doc', 'docx', 'txt', 'rtf');
    if (!in_array($file_ext, $allowed_extensions)) {
        $_SESSION['error'] = "File extension not allowed. Please upload a PDF, DOC, DOCX, TXT, or RTF file.";
        header("Location: dashboard.php");
        exit;
    }
    
    // Create the uploads directory if it doesn't exist
    $upload_dir = 'uploads/resumes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate a unique directory for this user to avoid filename collisions
    $user_upload_dir = $upload_dir . $user_id . '/';
    if (!is_dir($user_upload_dir)) {
        mkdir($user_upload_dir, 0755, true);
    }
    
    // Final path where the file will be stored
    $file_path = $user_upload_dir . $file_name;
    
    // If a file with the same name exists, add a number to make it unique
    $count = 1;
    $original_name = pathinfo($file_name, PATHINFO_FILENAME);
    while (file_exists($file_path)) {
        $new_file_name = $original_name . '_' . $count . '.' . $file_ext;
        $file_path = $user_upload_dir . $new_file_name;
        $count++;
    }
    
    // Get the final filename (with or without the counter)
    $final_filename = basename($file_path);
    
    // Path to store in the database (relative path)
    $db_file_path = $user_upload_dir . $final_filename;
    
    // Move the uploaded file to the destination
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Delete the old resume file if it exists
        $query = "SELECT Resume FROM users WHERE User_ID = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && !empty($user['Resume']) && file_exists($user['Resume'])) {
            unlink($user['Resume']);
        }
        
        // Update the database with the new resume file path
        $update_query = "UPDATE users SET Resume = ? WHERE User_ID = ?";
        $update_stmt = $con->prepare($update_query);
        $update_stmt->bind_param("si", $db_file_path, $user_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Resume uploaded successfully!";
        } else {
            $_SESSION['error'] = "Failed to update the database: " . $update_stmt->error;
        }
    } else {
        $_SESSION['error'] = "Failed to upload the file.";
    }
} else {
    $_SESSION['error'] = "No file uploaded or an error occurred.";
}

// Close the database connection
$con->close();

// Redirect back to the dashboard
header("Location: employee_dashboard.php");
exit;
?>