<?php
// Include database configuration and start session
include 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Your PHP code for retrieving job listings goes here
// For example:
$query = "SELECT * FROM jobs";
$result = mysqli_query($conn, $query);

// Check if any jobs are available
if (mysqli_num_rows($result) > 0) {
    // Display job listings
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<h2>" . $row['job_title'] . "</h2>";
        echo "<p>" . $row['job_description'] . "</p>";
        // Add apply button/link for each job
        echo "<a href='apply.php?id=" . $row['job_id'] . "'>Apply</a><br>";
    }
} else {
    echo "No jobs available";
}
?>

<!-- Job application form -->
<form action="apply.php" method="post">
    <h2>Apply for a Job</h2>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>
    <label for="resume">Resume:</label>
    <input type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required><br>
    <button type="submit">Submit Application</button>
</form>
