<?php
// This script serves PDF files with proper headers

// Get the file path from the query string
$file = isset($_GET['file']) ? $_GET['file'] : '';

// Validate the file path (basic security check)
if (empty($file) || !file_exists($file) || !is_file($file)) {
    header("HTTP/1.0 404 Not Found");
    echo "File not found";
    exit;
}

// Check if it's a PDF file
$mime_type = mime_content_type($file);
$allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

if (!in_array($mime_type, $allowed_types)) {
    header("HTTP/1.0 403 Forbidden");
    echo "Not a valid document file";
    exit;
}

// Set the appropriate headers
header('Content-Type: ' . $mime_type);
header('Content-Disposition: inline; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: public, max-age=86400');
header('Pragma: public');

// Output the file
readfile($file);
exit;
?>