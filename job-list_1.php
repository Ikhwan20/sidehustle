<?php
include("session_handler.php");
include("connection.php");
include("functions.php");

echo json_encode(['loggedIn' => isset($_SESSION['user_id'])]);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = "SELECT * FROM jobs WHERE Active = 1";

if (!empty($search)) {
    $query .= " AND LOWER(Title) LIKE LOWER('%" . mysqli_real_escape_string($con, $search) . "%')";
}

$query .= " ORDER BY Title ASC";
$result = mysqli_query($con, $query);

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

        .btn-clear-search {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #aaa;
            font-size: 18px;
            cursor: pointer;
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
        
         /* Responsive design */
         @media (max-width: 768px) {
            .job-item {
                flex-direction: column;
            }
            .modal-content {
                width: 90%;
            }
        }
        @media (max-width: 576px) {
            .job-item .text-start {
                padding: 0 15px;
            }
            .job-item .btn {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <!--navbar-->
        <?php include("navbar.php"); ?>


         <!-- Title Start -->
        <div class="container-xxl py-5 bg-dark page-header-job mb-5">
            <div class="container my-5 pt-5 pb-4">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Browse Jobs</h1>
            </div>
        </div>

        <div class="container-xxl py-5">
            <div class="container">
                <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Job Listing</h1><br>
                <h2 class="text-center mb-1">Browse Your Prefered Job Here</h2><br>

                <!-- Search panel -->
                <div class="row mb-4" style="justify-content: center;">
                    <div class="col-md-5 position-relative">
                        <input type="text" class="form-control" id="searchJobs" placeholder="Search for jobs..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button class="btn btn-clear-search" id="clearSearchBtn">&times;</button>                        </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" onclick="filterJobs()" style="background-color: #FE7A36;">Search</button>
                    </div>
                </div>

                <div class="tab-class text-center wow fadeInUp">
                    <!--job list-->
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <?php
                        $searchQuery = "";
                        if (isset($_GET['search']) && !empty($_GET['search'])) {
                            $searchTerm = mysqli_real_escape_string($con, $_GET['search']);
                            $searchQuery = "WHERE Title LIKE '%$searchTerm%'";
                        }
                        
                        $query = "SELECT * FROM jobs $searchQuery ORDER BY Title ASC";
                        
                        $result = mysqli_query($con, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<div class="job-item p-4 mb-4">';
                                echo '<div class="row g-4">';
                                echo '<div class="col-sm-12 col-md-8 d-flex align-items-center">';
                                echo '<div class="text-start ps-4">';
                                echo '<h5 class="mb-3">' . $row['Title'] . '</h5>';
                        
                                // Check if there is a company name to decide whether to show the company icon
                                if (!empty($row['Company'])) {
                                    echo '<span class="text-truncate me-3"><i class="fa fa-building text-primary me-2"></i>' . $row['Company'] . '</span><br>';
                                }
                        
                                echo '<span class="text-truncate me-3"><i class="fa fa-info-circle text-primary me-2"></i>' . $row['Description'] . '</span><br>';
                                echo '<span class="text-truncate me-3"><i class="fa fa-map-marker-alt text-primary me-2"></i>' . $row['Location'] . '</span>';
                                echo '<span class="text-truncate me-0"><i class="far fa-money-bill-alt text-primary me-2"></i>RM ' . $row['Salary'] . '</span><br>';
                                echo '<span class="text-truncate me-0"><i class="fa fa-info-circle text-primary me-2"></i> ' . $row['WorkDate'] . ' (' . $row['Duration'] . ' days)</span>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="col-sm-12 col-md-4 d-flex flex-column align-items-start align-items-md-end justify-content-center">';
                                echo '<div class="d-flex mb-3">';
                                echo '<button class="btn btn-orange details-btn" data-title="' . $row['Title'] . '" data-company="' . $row['Company'] . '" data-description="' . $row['Description'] . '" data-location="' . $row['Location'] . '" data-salary="' . $row['Salary'] . '">View Details</button>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No jobs found.</p>';
                        }
                        mysqli_close($con);
                        ?>

                        <!-- Job details modal Start -->
                        <div class="modal" id="details-modal">
                            <div class="modal-content">
                                <span class="close" id="close-details-modal">&times;</span>
                                <h4 class="mb-3" id="modal-title"></h4>
                                <p id="modal-company"></p>
                                <p id="modal-description"></p>
                                <p id="modal-location"></p>
                                <p id="modal-salary"></p>
                                <button id="apply-now-btn" class="btn btn-orange">Apply Now</button>
                            </div>
                        </div>

                        <div class="modal" id="login-modal">
                            <div class="modal-content">
                                <span class="close" id="close-login-modal">&times;</span>
                                <h4 class="mb-3">Log In</h4>
                                <p>Please <a href="login.php">log in</a> to apply for this job.</p>
                            </div>
                        </div>

                        <div class="modal" id="register-modal">
                            <div class="modal-content">
                                <span class="close" id="close-register-modal">&times;</span>
                                <h4 class="mb-3">Register</h4>
                                <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h5 class="text-white mb-4">Company</h5>
                        <a class="btn btn-link text-white-50" href="./index.php">Home</a>
                        <a class="btn btn-link text-white-50" href="./jobs-list_1.html">Job Lists</a>
                        <a class="btn btn-link text-white-50" href="./terms_conditions.html">Terms & Condition</a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h5 class="text-white mb-4">Quick Links</h5>
                        <a class="btn btn-link text-white-50" href="./index.php">Home</a>
                        <a class="btn btn-link text-white-50" href="./about.html">About Us</a>
                        <a class="btn btn-link text-white-50" href="./contact.html">Contact Us</a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h5 class="text-white mb-4">Contact Us</h5>
                        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>05 - 468 888</p>
                        <p class="mb-2"><i class="fa fa-envelope me-3"></i>SideHustle@gmail.com</p>
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

    <a href="#" class="btn btn-lg btn-orange btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Check if user logged in
        $(document).ready(function() {
            const userLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
            const username = <?php echo isset($_SESSION['user']) ? json_encode($_SESSION['user']['username']) : 'null'; ?>;

            if (userLoggedIn) {
                $('#guest-nav').hide();
                $('#user-nav').show();
                $('#username-display').text(username);
            } else {
                $('#guest-nav').show();
                $('#user-nav').hide();
            }

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

                $('#details-modal').fadeIn();
            });

            $('#apply-now-btn').click(function() {
                if (loggedIn) {
                    selectedJobId = $('#details-modal').find('.details-btn').data('job-id');
                    $('#details-modal').fadeOut();
                    $('#particulars-modal').fadeIn();
                } else {
                    $('#details-modal').fadeOut();
                    $('#login-modal').fadeIn();
                }
            });

            $('#close-details-modal, #close-login-modal, #close-register-modal, #close-particulars-modal').click(function() {
                $('.modal').fadeOut();
            });

            $('#logout-btn').click(function() {
                $.post('logout.php', function() {
                    window.location.reload();
                });
            });

            $('#particulars-form').submit(function(e) {
                e.preventDefault();

                const jobId = selectedJobId;
                const phone = $('#phone').val();
                const address = $('#address').val();

                $.post('submit_application.php', { job_id: jobId, phone: phone, address: address }, function(response) {
                    alert('Application submitted successfully!');
                    $('#particulars-modal').fadeOut();
                }).fail(function() {
                    alert('Failed to submit application. Please try again.');
                });
            });
            
            // Clear search input and restore job list
                $('#clearSearchBtn').click(function() {
                    $('#searchJobs').val(''); // Clear the search input
                    filterJobs(); // Call filterJobs to restore job list
            });
                });

            // To find jobs
            function filterJobs() {
                let searchQuery = document.getElementById("searchJobs").value.trim();
                window.location.href = "job-list_1.php?search=" + encodeURIComponent(searchQuery);
            }

    </script>
</body>
</html>
