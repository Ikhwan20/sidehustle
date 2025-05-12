<?php
include("session_handler.php");
include("connection.php");
include("functions.php");

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
                        <button class="btn btn-primary w-100" id="search-button" style="background-color: #FE7A36;">Search</button>
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
                                echo '<span class="text-truncate me-0"><i class="fa fa-info-circle text-primary me-2"></i> ' . $row['WorkDate'] . ' (' . $row['Duration'] . ' month(s))</span>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="col-sm-12 col-md-4 d-flex flex-column align-items-start align-items-md-end justify-content-center">';
                                echo '<div class="d-flex mb-3">';
                                echo '<span><button class="btn btn-orange details-btn" data-job-id="' . htmlspecialchars($row['Job_ID']) . '" data-title="' . htmlspecialchars($row['Title']) . '" data-description="' . htmlspecialchars($row['Description']) . '" data-location="' . htmlspecialchars($row['Location']) . '" data-salary="' . htmlspecialchars($row['Salary']) . '">View Details</button></span>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No jobs found.</p>';
                        }
                        ?>
                    </div>
                    
                    
                    <!-- Job Details Modal -->
                    <div id="details-modal" class="modal fade" tabindex="-1" aria-labelledby="details-modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-title"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p id="modal-description"></p>
                                    <p id="modal-location"></p>
                                    <p id="modal-salary"></p>
                                    
                                    <?php
                                    // Fetch the current user's information to pre-fill the form
                                    if(isset($_SESSION['User_ID'])) {
                                        $user_id = $_SESSION['User_ID'];
                                        $current_user_query = "SELECT Username, Email, Resume FROM users WHERE User_ID = ?";
                                        $current_user_stmt = $con->prepare($current_user_query);
                                        $current_user_stmt->bind_param("i", $user_id);
                                        $current_user_stmt->execute();
                                        $current_user_result = $current_user_stmt->get_result();
                                        $current_user = $current_user_result->fetch_assoc();
                                        $current_user_stmt->close();
                                    }
                                    ?>

                                    <!-- Add application form -->
                                    <form id="application-form" action="submit_application.php" method="POST" enctype="multipart/form-data" style="display:none;">
                                        <input type="hidden" id="job-id-input" name="job-id">
                                        <div class="mb-3">
                                            <label for="full-name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="full-name" name="full-name" value="<?php echo isset($current_user) ? htmlspecialchars($current_user['Username']) : ''; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($current_user) ? htmlspecialchars($current_user['Email']) : ''; ?>" required>
                                        </div>
                                        
                                        <?php if (isset($current_user) && !empty($current_user['Resume'])): ?>
                                            <!-- If user has a resume, use it automatically -->
                                            <div hidden class="mb-3">
                                                <label class="form-label">Resume</label>
                                                <div class="alert alert-success">
                                                    <p>We will use your existing resume on file.</p>
                                                    <input type="hidden" name="use_existing_resume" value="1">
                                                    <input type="hidden" name="existing_resume_path" value="<?php echo htmlspecialchars($current_user['Resume']); ?>">
                                                    <?php if (file_exists($current_user['Resume'])): ?>
                                                        <a href="view_resume.php?file=<?php echo urlencode($current_user['Resume']); ?>" target="_blank" class="btn btn-sm btn-info">View Resume</a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="upload-new-resume">
                                                    <label class="form-check-label" for="upload-new-resume">
                                                        Upload a different resume instead
                                                    </label>
                                                </div>
                                                <div id="new-resume-upload" style="display:none;" class="mt-2">
                                                    <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                                    <small class="text-muted">Please upload your resume (PDF, DOC, or DOCX)</small>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- If no resume found, show upload field -->
                                            <div hidden class="mb-3">
                                                <label for="resume" class="form-label">Resume</label>
                                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                                                <small class="text-muted">Please upload your resume (PDF, DOC, or DOCX)</small>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <button type="submit" class="btn btn-orange">Submit Application</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button id="apply-now-btn" class="btn btn-orange">Apply Now</button>
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

        <a href="#" class="btn btn-lg btn-orange btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="js/main.js"></script>
        <script>
            // Replace the existing JavaScript section at the bottom of the file
            function filterJobs() {
                let searchQuery = document.getElementById("searchJobs").value.trim();
                window.location.href = "job-list_1.php?search=" + encodeURIComponent(searchQuery);
            }

            $(document).ready(function() {
                $('.details-btn').click(function() {
                    var jobId = $(this).data('job-id');
                    var title = $(this).data('title');
                    var description = $(this).data('description');
                    var location = $(this).data('location');
                    var salary = $(this).data('salary');

                    // Store job ID in the modal
                    $('#details-modal').data('job-id', jobId);

                    $('#modal-title').text(title);
                    $('#modal-description').text('Description: ' + description);
                    $('#modal-location').text('Location: ' + location);
                    $('#modal-salary').text('Salary: RM ' + salary);

                    $('#details-modal').modal('show');
                });

                $('#apply-now-btn').click(function() {
                    const userLoggedIn = <?php echo isset($_SESSION['User_ID']) ? 'true' : 'false'; ?>;
                    
                    if (userLoggedIn) {
                        // Show application form
                        $('#application-form').show();
                        $(this).hide();
                        
                        // Set the job ID in the hidden field
                        var jobId = $('#details-modal').data('job-id');
                        $('#job-id-input').val(jobId);
                    } else {
                        $('#details-modal').modal('hide');
                        window.location.href = 'login.php';
                    }
                });

                // Add form submission handler
                $('#application-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    var formData = new FormData(this);
                    
                    $.ajax({
                        url: 'submit_application.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(result) {
                            if(result.status === 'success') {
                                alert(result.message);
                                // Use Bootstrap's modal hide method instead of fadeOut
                                $('#details-modal').modal('hide');
                            } else {
                                alert(result.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("XHR Status:", status);
                            console.error("Error:", error);
                            console.log("Response text:", xhr.responseText);
                            
                            alert('Your application has been submitted, but there was an issue with the confirmation.');
                            // Also use modal('hide') here
                            $('#details-modal').modal('hide');
                        }
                    });
                });
            });
            document.addEventListener('DOMContentLoaded', function() {
                const uploadNewResumeCheckbox = document.getElementById('upload-new-resume');
                if (uploadNewResumeCheckbox) {
                    uploadNewResumeCheckbox.addEventListener('change', function() {
                        const newResumeUploadDiv = document.getElementById('new-resume-upload');
                        const resumeInput = document.querySelector('#new-resume-upload #resume');
                        
                        if (this.checked) {
                            newResumeUploadDiv.style.display = 'block';
                            resumeInput.required = true;
                            document.querySelector('input[name="use_existing_resume"]').value = '0';
                        } else {
                            newResumeUploadDiv.style.display = 'none';
                            resumeInput.required = false;
                            document.querySelector('input[name="use_existing_resume"]').value = '1';
                        }
                    });
                }
            });
        </script>
    </div>
</body>
</html>
<?php
mysqli_close($con);
?>
