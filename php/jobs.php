<?php
// Connect to MySQL
include 'config.php';

// SQL query to retrieve job listings
$sql = "SELECT * FROM jobs";

// Execute the query
$result = $conn->query($sql);

// Initialize an array to store job listings
$jobListings = [];

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Loop through the result set and store each row in the $jobListings array
    while ($row = $result->fetch_assoc()) {
        $jobListings[] = $row;
    }
} else {
    echo "No jobs available";
}

// Close the database connection
$conn->close();
?>
