<?php
session_start();

include("connection.php");
include("functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $resume = $_FILES['resume'];
    $captured_image = $_POST['captured-image'];

    // Save the resume file
    $resume_dir = "uploads/resumes/";
    $resume_path = $resume_dir . basename($resume["name"]);
    move_uploaded_file($resume["tmp_name"], $resume_path);

    // Save the profile picture if captured image is provided
    if ($captured_image) {
        $profile_pic_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $captured_image));
        $profile_pic_path = "uploads/profile_pics/" . $username . ".jpg";
        file_put_contents($profile_pic_path, $profile_pic_data);
    } elseif (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] == UPLOAD_ERR_OK) {
        // Save the uploaded profile picture if no captured image
        $profile_pic_dir = "uploads/profile_pics/";
        $profile_pic_path = $profile_pic_dir . basename($_FILES["profile-pic"]["name"]);
        move_uploaded_file($_FILES["profile-pic"]["tmp_name"], $profile_pic_path);
    }

    // Insert user data into the database
    $sql = "INSERT INTO users (Username, Email, Password, Gender, Age, Resume, ProfilePic)
            VALUES ('$username', '$email', '$password', '$gender', '$age', '$resume_path', '$profile_pic_path')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
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
                <a href="index.html" class="nav-item nav-link active">Home</a>
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
                        <button type="button" class="btn btn-secondary ms-3" onclick="openCamera()">Take Picture</button>
                    </div>
                    <div class="form-group mb-3">
                        <video id="video" width="320" height="240" autoplay style="display: none;"></video>
                        <canvas id="canvas" width="320" height="240" style="display: none;"></canvas>
                    </div>
                    <div class="form-group mb-3">
                        <label>Image Preview:</label><br>
                        <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 320px; max-height: 240px;">
                    </div>
                    <input type="hidden" id="captured-image" name="captured-image">
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

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $resume = $_FILES['resume'];
    $captured_image = $_POST['captured-image'];

    // Save the resume file
    $resume_dir = "uploads/resumes/";
    $resume_path = $resume_dir . basename($resume["name"]);
    move_uploaded_file($resume["tmp_name"], $resume_path);

    // Save the profile picture if captured image is provided
    if ($captured_image) {
        $profile_pic_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $captured_image));
        $profile_pic_path = "uploads/profile_pics/" . $username . ".png";
        file_put_contents($profile_pic_path, $profile_pic_data);
    } elseif (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] == UPLOAD_ERR_OK) {
        // Save the uploaded profile picture if no captured image
        $profile_pic_dir = "uploads/profile_pics/";
        $profile_pic_path = $profile_pic_dir . basename($_FILES["profile-pic"]["name"]);
        move_uploaded_file($_FILES["profile-pic"]["tmp_name"], $profile_pic_path);
    }

    // Connect to the database
    $conn = new mysqli('localhost', 'username', 'password', 'database');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert user data into the database
    $sql = "INSERT INTO users (Username, Email, Password, Gender, Age, Resume, ProfilePic)
            VALUES ('$username', '$email', '$password', '$gender', '$age', '$resume_path', '$profile_pic_path')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
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
                <a href="index.html" class="nav-item nav-link active">Home</a>
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
    <button type="button" class="btn btn-secondary ms-3" onclick="openCamera()">Take Picture</button>
</div>

<div class="form-group mb-3">
    <video id="video" width="320" height="240" autoplay style="display: none;"></video>
    <canvas id="canvas" width="320" height="240" style="display: none;"></canvas>
</div>

<div class="form-group mb-3">
    <label>Image Preview:</label><br>
    <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 320px; max-height: 240px;">
</div>

<input type="hidden" id="captured-image" name="captured-image">
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
    function openCamera() {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            const video = document.getElementById('video');
            video.style.display = 'block';
            video.srcObject = stream;
        })
        .catch(error => {
            console.error('Error accessing webcam:', error);
        });
}

function captureImage() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    const imageDataUrl = canvas.toDataURL('image/png');

    // Set the image preview
    const imagePreview = document.getElementById('image-preview');
    imagePreview.src = imageDataUrl;
    imagePreview.style.display = 'block';

    // Store the captured image in the hidden input field
    document.getElementById('captured-image').value = imageDataUrl;
}

function previewProfilePic(event) {
    const input = event.target;
    const reader = new FileReader();
    reader.onload = function(){
        const imagePreview = document.getElementById('image-preview');
        imagePreview.src = reader.result;
        imagePreview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

document.getElementById('video').addEventListener('click', captureImage);
</script>
</body>
</html>