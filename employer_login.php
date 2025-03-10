<?php
session_start();

include("functions.php");
include("connection.php");

// Handle Sign In
if($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get form data
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Check if login and password are provided
    if(!empty($login) &&!empty($password)) {
        // Check if login is an email or username
        if(filter_var($login, FILTER_VALIDATE_EMAIL)) {
            // Login is an email
            $query = "SELECT * FROM employers WHERE Email='$login' AND Password='$password'";
        } else {
            // Login is a username
            $query = "SELECT * FROM employers WHERE Username='$login' AND Password='$password'";
        }

        $result = mysqli_query($con, $query);

        if(mysqli_num_rows($result) == 1) {
            // Fetch employer's data
            $employerData = mysqli_fetch_assoc($result);
            
            // Set session variables for authenticated user
            $_SESSION['employer_id'] = $employerData['Employer_ID'];
            $_SESSION['employer_username'] = $employerData['Username'];
            
            // Redirect to employer dashboard page
            header("Location: employer_dashboard.php");
            die;
        } else {
            // Invalid credentials, display error message
            echo "<script>alert('Invalid email or username or password. Please try again.');</script>";
        }
    } else {
        // Invalid form data, display error message
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employer Login</title>
    <!-- Include necessary CSS and JS files -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-xxl bg-white p-0">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
            <div class="container-fluid">
                <a href="index.php" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
                    <h1 class="m-0" style="color: #FE7A36;">Side Hustle</h1>
                </a>
            </div>
        </nav>

    <!-- Login Form -->
    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Employer Login</h1>
            <div class="row g-4">
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
                <div class="col-md-6">
                    <div class="wow fadeInUp" data-wow-delay="0.5s">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="login" class="form-control" placeholder="Email or Username" required>
                                        <label for="login">Email or Username</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="password" name="password" class="form-control" placeholder="Password" required>
                                        <label for="password">Password</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn w-100 py-3" type="submit" style="background-color: #FE7A36; color: white;">Sign In</button>
                                    <br><br><h6 style="text-align: center;">Don't have an account? <a href="employer_registration.php" style="color: #FE7A36; text-align: center;">Sign Up here.</a></h6>
                                </div><br><br><br><br><br><br><br><br><br><br>
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
