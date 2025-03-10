<?php
session_start();
include("connection.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
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
    <style>
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-orange {
            background-color: #FE7A36;
            color: white;
            transition: background-color 0.3s ease;
        }
        .btn-orange:hover {
            background-color: #D96127;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            position: relative;
            margin: auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            background-color: #fff;
            border-radius: 8px;
            animation: modalopen 0.4s;
        }
        .modal-content {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            position: fixed;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        @keyframes modalopen {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.8);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
            <div class="container-fluid">
                <a href="index.php" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
                    <h1 class="m-0" style="color: #FE7A36;">Side Hustle</h1>
                </a>
                <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav ms-auto p-4 p-lg-0">
                        <!-- Display welcome message with username -->
                        <a class="nav-link" href="#" id="username" role="button">
                            Welcome, <?php echo htmlspecialchars($_SESSION['Username']); ?>
                        </a>                
                        <li class="nav-item">
                            <a href="index.php" class="nav-link active">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="employee_dashboard.php" class="nav-link">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">Log Out</a>
                        </li>
                    </ul>
                </div>
            </div>
    </nav>

    <div class="container-xxl py-5 bg-dark page-header-job mb-5">
        <div class="container my-5 pt-5 pb-4">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Browse Jobs</h1>
        </div>
        </div>

    <!-- Job Listing Section -->
    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5">Job Listing</h1>
            <div class="row">
                <?php
                $query = "SELECT * FROM jobs ORDER BY Title ASC";
                $result = mysqli_query($con, $query);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="col-lg-4 col-md-6 mb-4">';
                        echo '<div class="job-item p-4 mb-4">';
                        echo '<h5>' . htmlspecialchars($row['Title']) . '</h5>';
                        echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['Description']) . '</p>';
                        echo '<p><strong>Location:</strong> ' . htmlspecialchars($row['Location']) . '</p>';
                        echo '<p><strong>Salary:</strong> RM ' . htmlspecialchars($row['Salary']) . '</p>';
                        echo '<button class="btn btn-orange details-btn" data-job-id="' . htmlspecialchars($row['Job_ID']) . '" data-title="' . htmlspecialchars($row['Title']) . '" data-description="' . htmlspecialchars($row['Description']) . '" data-location="' . htmlspecialchars($row['Location']) . '" data-salary="' . htmlspecialchars($row['Salary']) . '">View Details</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No jobs found.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div id="details-modal" class="modal">
        <div class="modal-content">
            <span id="close-details-modal" class="close">&times;</span>
            <h2 id="modal-title"></h2>
            <p id="modal-description"></p>
            <p id="modal-location"></p>
            <p id="modal-salary"></p>
            <button id="apply-now-btn" class="btn btn-orange">Apply Now</button>
        </div>
    </div>

    <!-- Application Modal -->
    <div id="application-modal" class="modal">
        <div class="modal-content">
            <span id="close-application-modal" class="close">&times;</span>
            <h2>Application Form</h2>
            <form id="application-form" method="post" action="submit_application.php" enctype="multipart/form-data">
                <input type="hidden" id="job-id" name="job-id" value="">
                <div class="mb-3">
                    <label for="full-name">Name</label>
                    <input type="text" class="form-control" id="full-name" name="full-name" required>
                </div>
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="resume">Upload Resume</label>
                    <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                </div>
                <button type="submit" class="btn btn-orange">Submit Application</button>
            </form>
        </div>
    </div>

    <a href="#" class="btn btn-lg btn-orange btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>

    <script>
        $(document).ready(function() {
            let selectedJobId = null;

            $('.details-btn').click(function() {
                const title = $(this).data('title');
                const company = $(this).data('company');
                const description = $(this).data('description');
                const location = $(this).data('location');
                const salary = $(this).data('salary');

                $('#modal-title').text(title);
                $('#modal-company').text(company);
                $('#modal-description').text(description);
                $('#modal-location').text(location);
                $('#modal-salary').text('RM ' + salary);

                selectedJobId = $(this).data('job-id');
                $('#details-modal').fadeIn();
            });

            $('#apply-now-btn').click(function() {
                $('#job-id').val(selectedJobId);
                $('#details-modal').fadeOut();
                $('#application-modal').fadeIn();
            });

            $('#close-details-modal, #close-application-modal').click(function() {
                $('.modal').fadeOut();
            });

            $('#application-form').submit(function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: 'submit_application.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alert('Application submitted successfully!');
                        $('#application-modal').fadeOut();
                    },
                    error: function() {
                        alert('Failed to submit application. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>