<?php
// Database connection
include 'config.php';


// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $pay = mysqli_real_escape_string($conn, $_POST['pay']);
    $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
    $employer_name = mysqli_real_escape_string($conn, $_POST['employer_name']);

    // Insert new job listing into database
    $sql = "INSERT INTO job_listings (title, description, location, deadline, pay, employer_name, contact_email) 
            VALUES ('$title', '$description', '$location', '$deadline', 'pay', '$employer_name', '$contact_email')";

    if ($conn->query($sql) === TRUE) {
        echo "New job listing added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close database connection
$conn->close();
?>