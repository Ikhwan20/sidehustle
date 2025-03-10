<?php
session_start();

// Check if the employer is logged in
if(!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

include("connection.php");
include("functions.php");

// Handle Job Post
if($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if form data is set
    if(isset($_POST['jobTitle']) && isset($_POST['companyName']) && isset($_POST['jobDescription']) && isset($_POST['jobRequirements']) && isset($_POST['salary']) && isset($_POST['location']) && isset($_POST['date'])) {
        // Get form data
        $jobTitle = $_POST['jobTitle'];
        $companyName = $_POST['companyName'];
        $jobDescription = $_POST['jobDescription'];
        $jobRequirements = $_POST['jobRequirements'];
        $salary = $_POST['salary'];
        $location = $_POST['location'];
        $date = $_POST['date'];

        // Check if form fields are not empty
        if(!empty($jobTitle) && !empty($jobDescription) && !empty($salary) && !empty($location) && !empty($date)) {
            // Save job post to database
            $employerId = $_SESSION['employer_id'];
            $query = "INSERT INTO jobs (Employer_ID, Company, Title, Description, JobRequirements, Salary, Location, WorkDate) VALUES ('$employerId', '$companyName', '$jobTitle', '$jobDescription', '$jobRequirements', '$salary', '$location', '$date')";

            if(mysqli_query($con, $query)) {
                // Set success message
                $_SESSION['success_message'] = "Job posted successfully!";
                // Redirect to job list page
                header("Location: employer_dashboard.php");
                die;
            } else {
                echo "<script>alert('Error saving job post to database. Please try again later.');</script>";
            }
        } else {
            echo "<script>alert('Please fill in all required fields.');</script>";
        }
    } else {
        echo "<script>alert('Form data is not set. Please try again later.');</script>";
    }
    if($employer_login_success) {
        $_SESSION['employer_id'] = $employer_id;
        $_SESSION['employer_name'] = $employer_name; // Set the employer name here
        header("Location: employer_dashboard.php");
        die;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Post a Job</title>
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
                <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav ms-auto p-4 p-lg-0">
                        <li class="nav-item">
                            <span class="nav-link" style="cursor: pointer; color: #FE7A36;">Welcome,  <?php echo$_SESSION['employer_username']; ?></span>
                        </li>
                        <li class="nav-item">
                            <a href="employer_dashboard.php" class="nav-link">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="post_jobs.php" class="nav-link">Post Jobs</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="logout-link nav-link" onclick="confirmLogout()">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    
        <!-- Job Post Form -->
    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Post a Job</h1>
            <div class="row g-4">
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
                <div class="col-md-6">
                    <div class="wow fadeInUp" data-wow-delay="0.5s">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="jobTitle" class="form-control" placeholder="Job Title" required>
                                        <label for="jobTitle">Job Title</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="companyName" class="form-control" placeholder="Company Name">
                                        <label for="companyName">Company Name</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="jobDescription" class="form-control" placeholder="Job Description" style="height: 150px;" required></textarea>
                                        <label for="jobDescription">Job Description</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="jobRequirements" class="form-control" placeholder="Job Requirements" style="height: 150px;"></textarea>
                                        <label for="jobRequirements">Job Requirements</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="salary" class="form-control" placeholder="Salary" required>
                                        <label for="salary">Salary</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="location" class="form-control" placeholder="Location" required>
                                        <label for="location">Location</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="date" type="date" name="date" class="form-control" placeholder="Date" required>
                                        <label for="date">Date</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                    <input type="number" id="duration" name="duration" class="form-control" placeholder="Duration (in days)" min="1"><br><br>
                                    <label for="duration">Job Duration</label>
                                </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="termsCheck" onchange="toggleSubmitButton()">
                                        <label class="form-check-label" for="termsCheck">
                                            I agree to the <a href="terms_conditions.html" target="_blank" style="color: #FE7A36;">terms and conditions</a>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button id="postJobButton" class="btn w-100 py-3" type="submit" style="background-color: #FE7A36; color: white;" disabled>Post Job</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
            </div>
        </div>
    </div>
</div>

<!-- Include necessary JS files -->
<script src="js/bootstrap.bundle.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/main.js"></script>
<script>
    function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        }
    function toggleSubmitButton() {
        const termsCheck = document.getElementById('termsCheck');
        const postJobButton = document.getElementById('postJobButton');
        postJobButton.disabled = !termsCheck.checked;
    }
</script>
</body>
</html>
