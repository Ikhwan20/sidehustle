<?php 
session_start();

include("functions.php");
include("connection.php");

// Handle Sign Up
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get form data and trim spaces
    $username = trim($_POST['username']);
    $companyName = trim($_POST['companyName']);
    $ssmNumber = trim($_POST['ssmNumber']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    // Check if form fields are not empty
    if (!empty($username) && !empty($companyName) && !empty($ssmNumber) && !empty($email) && !empty($password)) {

        // Ensure SSM number is numeric and between 10-12 characters
        if (!is_numeric($ssmNumber) || strlen($ssmNumber) < 10 || strlen($ssmNumber) > 12) {
            echo "<script>alert('SSM Number must be 10-12 digits long.');</script>";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.');</script>";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Sanitize input before inserting into database
            $username = mysqli_real_escape_string($con, $username);
            $companyName = mysqli_real_escape_string($con, $companyName);
            $ssmNumber = mysqli_real_escape_string($con, $ssmNumber);
            $email = mysqli_real_escape_string($con, $email);
            $phone = mysqli_real_escape_string($con, $phone);

            // Save data to database
            $query = "INSERT INTO employers (username, CompanyName, SSMNumber, Email, Password, Phone) 
                      VALUES ('$username', '$companyName', '$ssmNumber', '$email', '$hashedPassword', '$phone')";

            // Execute query
            if (mysqli_query($con, $query)) {
                // Redirect to login page
                header("Location: employer_login.php");
                die;
            } else {
                echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Please fill in all required fields correctly.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employer Registration</title>
    <!-- Include necessary CSS and JS files -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-xxl bg-white p-0">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
            <h1 class="m-0" style="color: #FE7A36;">Side Hustle</h1>
        </a>
    </nav>

    <!-- Sign Up Form -->
    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Employer Registration</h1>
            <div class="row g-4">
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
                <div class="col-md-6">
                    <div class="wow fadeInUp" data-wow-delay="0.5s">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="username" class="form-control" placeholder="Username" required>
                                        <label for="name">Username</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="companyName" class="form-control" placeholder="Company Name" required>
                                        <label for="companyName">Company Name</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="ssmNumber" class="form-control" placeholder="Company SSM Number" maxlength="12" required>
                                        <label for="ssmNumber">Company SSM Number</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="email" name="email" class="form-control" placeholder="Email" required>
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="password" name="password" class="form-control" placeholder="Password" required>
                                        <label for="password">Password</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="phone" class="form-control" placeholder="Phone Number">
                                        <label for="phone">Contact Number</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn w-100 py-3" type="submit" style="background-color: #FE7A36; color: white;">Sign Up</button>
                                    <br><br><h6 style="text-align: center;">Already have an account? <a href="employer_login.php" style="color: #FE7A36; text-align: center;">Sign In here.</a></h6>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include necessary JS files -->
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
