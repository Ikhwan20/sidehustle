<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Check if the employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

include("connection.php");

// Check for success or error message
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Fetch applicants for a specific job
if (isset($_GET['job_id'])) {
    $job_id = mysqli_real_escape_string($con, $_GET['job_id']);
    
    // Query to fetch job details
    $job_query = "SELECT * FROM jobs WHERE Job_ID = '$job_id'";
    $job_result = mysqli_query($con, $job_query);
    
    if (!$job_result) {
        die('Error fetching job details: ' . mysqli_error($con));
    }
    
    $job = mysqli_fetch_assoc($job_result);
    
    // Query to fetch applicants for the job
    $applicants_query = "SELECT job_applications.Application_ID, users.Username, users.Email, users.Resume, job_applications.Applied_At, job_applications.Status
                        FROM job_applications 
                        JOIN users ON job_applications.User_ID = users.User_ID 
                        WHERE job_applications.Job_ID = '$job_id'";
    $applicants_result = mysqli_query($con, $applicants_query);

    // Check if the query executed successfully
    if (!$applicants_result) {
        die('Error fetching applicants: ' . mysqli_error($con));
    }
    
    // Fetch status options from a predefined array or database table
    $status_options = array(
        'applied' => 'Applied',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'kiv' => 'KIV',
        'interview' => 'Interview'
    );
} else {
    header("Location: employer_dashboard.php");
    die;
}

// Process form submission to update application status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['application_id']) && isset($_POST['status'])) {
        $application_id = mysqli_real_escape_string($con, $_POST['application_id']);
        $status = mysqli_real_escape_string($con, $_POST['status']);
        
        // Update status in the database
        $update_query = "UPDATE job_applications SET Status = '$status' WHERE Application_ID = '$application_id'";
        $update_result = mysqli_query($con, $update_query);
        
        if ($update_result) {
            // Fetch applicant details for email notification
            $applicant_query = "SELECT users.Email, users.Username FROM job_applications 
                                JOIN users ON job_applications.User_ID = users.User_ID 
                                WHERE job_applications.Application_ID = '$application_id'";
            $applicant_result = mysqli_query($con, $applicant_query);
            
            if ($applicant_result && mysqli_num_rows($applicant_result) > 0) {
                $applicant_data = mysqli_fetch_assoc($applicant_result);
                $applicant_email = $applicant_data['Email'];
                $applicant_username = $applicant_data['Username'];

                // Send email notification using PHPMailer
                require 'vendor/autoload.php'; // Adjust path as necessary
                
                try {
                    // Instantiate PHPMailer
                    $mail = new PHPMailer(true);
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'puterifiqrina02@gmail.com'; // Your Gmail email address
                    $mail->Password = 'loza xapn grjh rxea'; // Your Gmail password or app-specific password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    
                    // Sender and recipient settings
                    $mail->setFrom('your_email@gmail.com', 'Your Name'); // Replace with your name
                    $mail->addAddress($applicant_email, $applicant_username);
                    
                    // Email content
                    $mail->isHTML(true);
                    $mail->Subject = 'Application Status Update';
                    $mail->Body = "Hello $applicant_username,<br><br>Your application status has been updated to $status.<br><br>Regards,<br>Your Employer";
                    
                    // Send email
                    $mail->send();
                    
                    $_SESSION['success_message'] = 'Application status updated successfully. Email sent to applicant.';
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Failed to send email. Error: {$mail->ErrorInfo}";
                    error_log("Failed to send email: {$e->getMessage()}");
                }
            } else {
                $_SESSION['error_message'] = 'Failed to fetch applicant details for email notification.';
            }
            
            header("Location: applicants.php?job_id=$job_id");
            die;
        } else {
            $_SESSION['error_message'] = 'Error updating application status: ' . mysqli_error($con);
            header("Location: applicants.php?job_id=$job_id");
            die;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Applicants - <?php echo htmlspecialchars($job['Title']); ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .logout-button {
            background-color: #FE7A36;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
            padding-right: 15px;
            width: 130px;
        }

        .navbar-nav {
            align-items: center;
        }

        .logout-button:hover {
            background-color: white;
            color: white;
        }

        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
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
                            <span class="nav-link" style="cursor: pointer; color: #FE7A36;">Welcome,  <?php echo $_SESSION['employer_username']; ?></span>
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

        <div class="container mt-4">
            <h1 class="mb-4">View Applicants for <?php echo htmlspecialchars($job['Title']); ?></h1>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Date Applied</th>
                                    <th>Resume</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($applicant = mysqli_fetch_assoc($applicants_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($applicant['Username']); ?></td>
                                        <td><?php echo htmlspecialchars($applicant['Email']); ?></td>
                                        <td><?php echo htmlspecialchars($applicant['Applied_At']); ?></td>
                                        <td>
                                        <?php if (!empty($applicant['Resume'])) : ?>
                            <p><a href="<?php echo $applicant['Resume']; ?>" target="_blank"><?php echo $applicant['Resume']; ?></a></p>
                        <?php else: ?>
                            <p>No resume uploaded</p>
                        <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" action="">
                                                <input type="hidden" name="application_id" value="<?php echo $applicant['Application_ID']; ?>">
                                                <select name="status" class="form-control">
                                                    <?php foreach ($status_options as $key => $value): ?>
                                                        <option value="<?php echo $key; ?>" <?php echo ($applicant['Status'] === $key) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                        </td>
                                        <td>
                                                <button type="submit" class="btn btn-primary mt-2">Update Status</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

    <script src="js/main.js"></script>
    <script>
        new WOW().init();

        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>