<?php

$dbhost = "localhost";
$dbuser = "myuser";
$dbpass = "mypassword";
$dbname = "sidehustledb";

// Establish database connection
if (!$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user']);
}
?>
