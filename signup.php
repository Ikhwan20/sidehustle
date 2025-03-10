<?php
session_start();

include("connection.php");
include("functions.php");

// Function to sanitize and validate input data
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST["Username"]);
    $email = sanitizeInput($_POST["Email"]);
    $password = sanitizeInput($_POST["Password"]);
    $gender = sanitizeInput($_POST["gender"]);
    $age = intval($_POST["age"]);

    // File upload handling
    $resume = "";
    if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == UPLOAD_ERR_OK) {
        $resumeName = $_FILES["resume"]["name"];
        $resumeTmpName = $_FILES["resume"]["tmp_name"];
        $resumeSize = $_FILES["resume"]["size"];
        $resumeType = $_FILES["resume"]["type"];
        $resumeError = $_FILES["resume"]["error"];

        if ($resumeError == UPLOAD_ERR_OK && in_array($resumeType, ["application/pdf"]) && $resumeSize <= 5 * 1024 * 1024) {
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777);
            }
            $resumeDest = "uploads/" . basename($resumeName);
            if (move_uploaded_file($resumeTmpName, $resumeDest)) {
                $resume = $resumeDest;
            } else {
                echo "Error uploading resume.";
                exit();
            }
        } else {
            echo "Invalid resume file.";
            exit();
        }
    }

    // Profile picture handling
    $profilePic = "";
    if (isset($_FILES["profile-pic"]) && $_FILES["profile-pic"]["error"] == UPLOAD_ERR_OK) {
        $profilePicName = $_FILES["profile-pic"]["name"];
        $profilePicTmpName = $_FILES["profile-pic"]["tmp_name"];
        $profilePicSize = $_FILES["profile-pic"]["size"];
        $profilePicType = $_FILES["profile-pic"]["type"];

        if (in_array($profilePicType, ["image/jpeg", "image/png"]) && $profilePicSize <= 2 * 1024 * 1024) {
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777);
            }
            $profilePicDest = "uploads/" . basename($profilePicName);
            if (move_uploaded_file($profilePicTmpName, $profilePicDest)) {
                $profilePic = $profilePicDest;
            } else {
                echo "Error uploading profile picture.";
                exit();
            }
        } else {
            echo "Invalid profile picture file.";
            exit();
        }
    }

    // Insert new record into users table
    $insertQuery = "INSERT INTO users (Username, Email, Password, Gender, Age, Resume, ProfilePic) VALUES ('$username', '$email', '$password', '$gender', $age, '$resume', '$profilePic')";
    if ($con->query($insertQuery) === TRUE) {
        $_SESSION['signup_success'] = true;
        header("Location: login.php");
        exit();
    } else {
        echo "Error registering user: " . $con->error;
    }
    $con->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Side Hustle</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
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
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link active">Home</a>
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="job-list.html" class="nav-item nav-link">Job</a>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
                <a href="./job-form.html" class="btn rounded-0 py-4 px-lg-5 d-none d-lg-block" style="color: white; background-color: #FE7A36;">Employer<i class="fa fa-arrow-right ms-3"></i></a>
            </div>
        </div>
    </nav>

    <!-- Sign Up Form Start -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h2 class="text-center mb-4">Sign Up</h2>
                <form id="signup-form" method="post" action="signup.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="form-group mb-3">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="Username" id="Username" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="Email" id="Email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="Password" id="Password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Gender</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" required>
                            <label class="form-check-label" for="genderMale">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" required>
                            <label class="form-check-label" for="genderFemale">Female</label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="age">Age</label>
                        <input type="number" class="form-control" name="age" id="age" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="resume">Upload Resume</label>
                        <input type="file" class="form-control" id="resume" name="resume" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="profile-pic">Profile Picture</label><br>
                        <input type="file" id="profile-pic" name="profile-pic" accept="image/*" onchange="previewProfilePic(event)">
                    </div>
                    <div class="form-group mb-3">
                        <label>Image Preview:</label><br>
                        <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 320px; max-height: 240px;">
                    </div>

                    <div class="form-group mb-3">
                        <button class="btn btn-primary w-100 py-3" type="submit">Sign Up</button>
                    </div>
                </form>
                <div id="signup-message" class="mt-3"></div>
            </div>
        </div>
    </div>
    <!-- Sign Up Form End -->

    <!-- Footer -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Company</h5>
                    <a class="btn btn-link text-white-50" href="./index.html">Home</a>
                    <a class="btn btn-link text-white-50" href="./job-list.html">Job Lists</a>
                    <a class="btn btn-link text-white-50" href="./job-form.html">Post Job</a>
                    <a class="btn btn-link text-white-50" href="">Terms & Condition</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <a class="btn btn-link text-white-50" href="./index.html">Home</a>
                    <a class="btn btn-link text-white-50" href="./about.html">About Us</a>
                    <a class="btn btn-link text-white-50" href="./contact.html">Contact Us</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Contact Us</h5>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>(00) 123 456</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>SideHustle@gmail.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">SideHustle</a> Unlocking Opportunities.                            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

    <script>
        function previewProfilePic(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('image-preview');
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>
</body>
</html>