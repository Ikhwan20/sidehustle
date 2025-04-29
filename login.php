<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("connection.php");
include("functions.php");

$response = array('status' => '', 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["ajax"]) && $_POST["ajax"] == 'true') {
    if (isset($_POST["Username_or_Email"]) && isset($_POST["Password"])) {
        $usernameOrEmail = sanitizeInput($_POST["Username_or_Email"]);
        $password = sanitizeInput($_POST["Password"]);

        // Prepare query (DO NOT include password in query comparison)
        $query = "SELECT User_ID, Username, Password FROM users WHERE Username = ? OR Email = ?";
        $stmt = $con->prepare($query);
        if ($stmt === false) {
            $response['status'] = 'error';
            $response['message'] = 'Database prepare error: ' . htmlspecialchars($con->error);
            echo json_encode($response);
            exit;
        }
        
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        if ($stmt === false) {
            $response['status'] = 'error';
            $response['message'] = 'Database execute error: ' . htmlspecialchars($con->error);
            echo json_encode($response);
            exit;
        }
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $username, $hashed_password);
            $stmt->fetch();

            // Verify the entered password with the hashed password from the database
            if (password_verify($password, $hashed_password)) {
                $_SESSION['User_ID'] = $user_id;
                $_SESSION['Username'] = $username;

                $response['status'] = 'success';
                $response['username'] = $username;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid username/email or password.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid username/email or password.';
        }
        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Please enter username/email and password!';
    }
    echo json_encode($response);
    exit;
}
$con->close();

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
            <a href="index.php" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
                <h1 class="m-0" style="color: #FE7A36;">Side Hustle</h1>
            </a>
            <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto p-4 p-lg-0">
                    <a href="index.php" class="nav-item nav-link active">Home</a>
                    <a href="about.html" class="nav-item nav-link">About</a>
                    <a href="contact.html" class="nav-item nav-link">Contact</a>
                    <div id="guest-nav">
                        <a href="login.php" class="nav-item nav-link" style="color: #FE7A36;">Sign In</a>
                    </div>
                    <div id="user-nav" style="display: none;">
                        <span class="nav-item nav-link dropdown-toggle" id="username" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            Welcome, <span id="username-display"></span>
                        </span>
                        <ul class="dropdown-menu" aria-labelledby="username">
                            <li><a class="dropdown-item" href="employee_dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="#" id="logout-btn">Log Out</a></li>
                        </ul>
                    </div>
                    <a href="employer_login.php" class="btn rounded-0 py-4 px-lg-5 d-none d-lg-block" style="color: white; background-color: #FE7A36;">Employer<i class="fa fa-arrow-right ms-3"></i></a>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Login Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Sign In</h1>
                <div class="row g-4">  
                    <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
                    <div class="col-md-6">
                        <div class="wow fadeInUp" data-wow-delay="0.5s">
                            <form id="loginForm" method="post">
                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                <?php endif; ?>
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="Username_or_Email" id="username_or_email" placeholder="Username or Email" required>
                                    <label for="username_or_email">Username or Email</label><br>
                                </div>
                                <div class="form-floating">
                                    <input type="password" class="form-control" name="Password" id="password" placeholder="Password" required>
                                    <label for="password">Password</label><br><br>
                                </div>
                                <button class="btn w-100 py-3" type="submit" style="background-color: #FE7A36; color: white;">Sign In</button>
                                <br><br>
                                <h6 style="text-align: center;">Don't have an account? <a href="signup.php" style="color: #FE7A36; text-align: center;">Sign Up here.</a></h6>
                            </form><br><br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Login End -->

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>

        <script>
            $(document).ready(function() {
                $('#loginForm').submit(function(event) {
                    event.preventDefault();
                    
                    var formData = $(this).serializeArray();
                    formData.push({name: 'ajax', value: 'true'}); // Flag for AJAX request
                    
                    $.ajax({
                        type: 'POST',
                        url: 'login.php',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                sessionStorage.setItem('isLoggedIn', 'true');
                                sessionStorage.setItem('username', response.username);
                                window.location.href = 'employee_dashboard.php';
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred while processing your request. Please try again.');
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>
    </div>
</body>
</html>