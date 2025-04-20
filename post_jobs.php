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
    // Validate and escape user inputs
    $employerId = $_SESSION['employer_id'];
    $jobTitle = mysqli_real_escape_string($con, $_POST['jobTitle']);
    $companyName = mysqli_real_escape_string($con, $_POST['companyName']);
    $jobDescription = mysqli_real_escape_string($con, $_POST['jobDescription']);
    $jobRequirements = mysqli_real_escape_string($con, $_POST['jobRequirements']);
    $salary = mysqli_real_escape_string($con, $_POST['salary']);
    $salaryUnit = mysqli_real_escape_string($con, $_POST['salaryUnit']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $date = mysqli_real_escape_string($con, $_POST['date']);
    $duration = mysqli_real_escape_string($con, $_POST['duration']);
    $jobType = mysqli_real_escape_string($con, $_POST['jobType']);
    $industry = mysqli_real_escape_string($con, $_POST['industry']);
    $experienceLevel = mysqli_real_escape_string($con, $_POST['experienceLevel']);
    $remoteOption = mysqli_real_escape_string($con, $_POST['remoteOption']);
    $skills = mysqli_real_escape_string($con, $_POST['skills']);
    $applicationDeadline = mysqli_real_escape_string($con, $_POST['applicationDeadline']);
    
    // Check if required fields are not empty
    if(!empty($jobTitle) && !empty($jobDescription) && !empty($salary) && !empty($location) && !empty($date)) {
        // Create SQL query
        $query = "INSERT INTO jobs (
            Employer_ID, Company, Title, Description, JobRequirements, 
            Salary, SalaryUnit, Location, WorkDate, Duration, JobType, 
            Industry, ExperienceLevel, RemoteOption, Skills, ApplicationDeadline
        ) VALUES (
            '$employerId', '$companyName', '$jobTitle', '$jobDescription', '$jobRequirements',
            '$salary', '$salaryUnit', '$location', '$date', '$duration', '$jobType',
            '$industry', '$experienceLevel', '$remoteOption', '$skills', 
            " . ($applicationDeadline ? "'$applicationDeadline'" : "NULL") . "
        )";

        if(mysqli_query($con, $query)) {
            $_SESSION['success_message'] = "Job posted successfully!";
            header("Location: employer_dashboard.php");
            die;
        } else {
            $error_message = "Error posting job: " . mysqli_error($con);
        }
    } else {
        $error_message = "Please fill in all required fields.";
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
                        <span class="nav-link" style="cursor: pointer; color: #FE7A36;">Welcome, <?php echo $_SESSION['employer_username']; ?></span>
                    </li>
                    <li class="nav-item">
                        <a href="employer_dashboard.php" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="post_jobs.php" class="nav-link active">Post Jobs</a>
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
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="row g-4">
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
                <div class="col-md-6">
                    <div class="wow fadeInUp" data-wow-delay="0.5s">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="jobTitle" class="form-control" placeholder="Job Title" required>
                                        <label for="jobTitle">Job Title *</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="companyName" class="form-control" placeholder="Company Name">
                                        <label for="companyName">Company Name</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="jobDescription" class="form-control" placeholder="Job Description" style="height: 150px;" required></textarea>
                                        <label for="jobDescription">Job Description *</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="jobRequirements" class="form-control" placeholder="Job Requirements" style="height: 150px;"></textarea>
                                        <label for="jobRequirements">Job Requirements</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input type="number" name="salary" class="form-control" placeholder="Salary" required min="0" step="0.01">
                                        <label for="salary">Salary *</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="salaryUnit" class="form-select" required>
                                            <option value="hour">Per Hour</option>
                                            <option value="day">Per Day</option>
                                            <option value="month" selected>Per Month</option>
                                        </select>
                                        <label for="salaryUnit">Salary Unit</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="location" class="form-control" placeholder="Location" required>
                                        <label for="location">Location *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" name="date" class="form-control" placeholder="Work Date" required>
                                        <label for="date">Work Date *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" name="duration" class="form-control" placeholder="Duration (in days)" min="1" value="1" required>
                                        <label for="duration">Duration (days) *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="jobType" class="form-select" required>
                                            <option value="Full-Time">Full-Time</option>
                                            <option value="Part-Time">Part-Time</option>
                                            <option value="Contract">Contract</option>
                                            <option value="Internship">Internship</option>
                                            <option value="Freelance">Freelance</option>
                                        </select>
                                        <label for="jobType">Job Type</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="industry" class="form-control" placeholder="Industry" required>
                                        <label for="industry">Industry *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="experienceLevel" class="form-select" required>
                                            <option value="Entry">Entry Level</option>
                                            <option value="Mid">Mid Level</option>
                                            <option value="Senior">Senior Level</option>
                                        </select>
                                        <label for="experienceLevel">Experience Level</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="remoteOption" class="form-select" required>
                                            <option value="On-Site">On-Site</option>
                                            <option value="Remote">Remote</option>
                                            <option value="Hybrid">Hybrid</option>
                                        </select>
                                        <label for="remoteOption">Work Location</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="skills" class="form-control" placeholder="Skills" style="height: 100px;"></textarea>
                                        <label for="skills">Skills</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="date" name="applicationDeadline" class="form-control" placeholder="Application Deadline">
                                        <label for="applicationDeadline">Application Deadline</label>
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
    
    // Optional: Set min date for application deadline to today
    document.addEventListener('DOMContentLoaded', function() {
        const deadlineInput = document.querySelector('input[name="applicationDeadline"]');
        const workDateInput = document.querySelector('input[name="date"]');
        
        const today = new Date().toISOString().split('T')[0];
        workDateInput.min = today;
        deadlineInput.min = today;
    });
</script>
</body>
</html>