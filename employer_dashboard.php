<?php
session_start();

// Check if the employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

include("connection.php");
include("functions.php");

// Check for success and error messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Handle state change request
if (isset($_POST['application_id']) && isset($_POST['new_state'])) {
    $application_id = $_POST['application_id'];
    $new_state = $_POST['new_state'];

// Update application state in database
$update_query = "UPDATE job_applications SET State = '$new_state' WHERE Application_ID = '$application_id'";
$update_result = mysqli_query($con, $update_query);

if ($update_result) {
    // Fetch applicant details for email notification
    $applicant_query = "SELECT * FROM job_applications WHERE Application_ID = '$application_id'";
    $applicant_result = mysqli_query($con, $applicant_query);

    if ($applicant_result && mysqli_num_rows($applicant_result) > 0) {
        $applicant_data = mysqli_fetch_assoc($applicant_result);
        $applicant_email = $applicant_data['Email'];
        $applicant_name = $applicant_data['Name'];

        // Send email notification
        $subject = 'Application Status Update';
        $message = "Hello $applicant_name,\n\nYour application status has been updated to $new_state.\n\nRegards,\nYour Employer";
        $headers = 'From: your-email@example.com'; 

        // Attempt to send email
        if (mail($applicant_email, $subject, $message, $headers)) {
            $_SESSION['success_message'] = 'Application status updated successfully. Email sent to applicant.';
            $email_sent = true; 
        } else {
            $_SESSION['error_message'] = 'Failed to send email notification to applicant. Please check server logs for details.';
            error_log("Failed to send email to $applicant_email");
            $email_sent = false;
        }
    } else {
        $_SESSION['error_message'] = 'Failed to fetch applicant details.';
        $email_sent = false; 
    }

    header("Location: employer_dashboard.php?email_sent=" . ($email_sent ? 'true' : 'false'));
    exit;
} else {
    $_SESSION['error_message'] = 'Failed to update application status.';
    header("Location: employer_dashboard.php");
    exit;
}
}

// Fetch jobs posted by the logged-in employer
$employerId = $_SESSION['employer_id'];
$jobs_query = "SELECT * FROM jobs WHERE Employer_ID = '$employerId'";
$jobs_query .= " ORDER BY Title ASC";
$jobs_result = mysqli_query($con, $jobs_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employer Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <!-- navbar -->
        <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
            <div class="container-fluid">
                <a href="employer_dashboard.php" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
                    <h1 class="m-0" style="color: #FE7A36;">Side Hustle</h1>
                </a>
                <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav ms-auto p-4 p-lg-0">
                        <li class="nav-item">
                            <span class="nav-link" style="cursor: pointer; color: #FE7A36;">Welcome,  <?php echo $_SESSION['employer_username']; ?></span>
                        </li>
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">Home</a>
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

        <div class="container-xxl py-5">
            <div class="container">
                <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Employer Dashboard</h1>

                <!--search panel -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="searchJobs" placeholder="Search for jobs...">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" onclick="filterJobs()">Search</button>
                    </div>
                </div>

                <!--jobs posted-->
                <?php if (mysqli_num_rows($jobs_result) > 0) : ?>
                <div class="row">
                    <?php while ($job = mysqli_fetch_assoc($jobs_result)) : ?>
                    <?php
                        // Fetch number of applications for this job
                        $job_id = $job['Job_ID'];
                        $applications_query = "SELECT COUNT(*) AS application_count FROM job_applications WHERE Job_ID = '$job_id'";
                        $applications_result = mysqli_query($con, $applications_query);
                        $application_data = mysqli_fetch_assoc($applications_result);
                        $application_count = $application_data['application_count'];
                        ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $job['Title']; ?></h5>
                                <p class="card-text"><?php echo $job['Description']; ?></p>
                                <p class="card-text"><small>Location: <?php echo $job['Location']; ?> | Salary:
                                        <?php echo $job['Salary']; ?> | Date: <?php echo $job['WorkDate']; ?></small>
                                </p>
                                <p class="card-text">Applications: <?php echo $application_count; ?></p>
                                <a href="edit_job.php?job_id=<?php echo $job_id; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_job.php?job_id=<?php echo $job_id; ?>" class="btn btn-danger btn-sm">Delete</a>
                                <a href="applicants.php?job_id=<?php echo $job_id; ?>" class="btn btn-info btn-sm">View Applications</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else : ?>
                <p>No jobs posted yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "employer_logout.php";
            }
        }

        function filterJobs() {
            const searchValue = document.getElementById('searchJobs').value.toLowerCase();
            const jobCards = document.querySelectorAll('.card');

            jobCards.forEach(card => {
                const title = card.querySelector('.card-title').innerText.toLowerCase();
                const description = card.querySelector('.card-text').innerText.toLowerCase();

                if (title.includes(searchValue) || description.includes(searchValue)) {
                    card.parentElement.style.display = 'block';
                } else {
                    card.parentElement.style.display = 'none';
                }
            });
        }

            // Function to show a Bootstrap alert
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alertDiv);
        setTimeout(() => {
            alertDiv.remove();
        }, 5000); // Automatically remove after 5 seconds
    }

    // Check if email_sent parameter is in URL and show appropriate alert
    const urlParams = new URLSearchParams(window.location.search);
    const emailSentParam = urlParams.get('email_sent');
    if (emailSentParam !== null) {
        if (emailSentParam === 'true') {
            showAlert('Email successfully sent to applicant.', 'success');
        } else {
            showAlert('Failed to send email to applicant. Please check server logs.', 'danger');
        }
    }
    </script>
</body>
</html>
