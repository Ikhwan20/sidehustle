<?php
// Include database configuration
include 'config.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if user exists in the database
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Start session and set session variables
        session_start();
        $_SESSION['username'] = $username;
        // Redirect to homepage
        header("Location: homepage.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
