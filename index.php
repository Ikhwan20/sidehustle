<?php
include("session_handler.php");
include("connection.php");
include("functions.php");

if (isset($_SESSION['User_ID'])) {
    $user_id = $_SESSION['User_ID'];
    // Prepare statement to fetch jobs matching user skills
    $query = "
        SELECT DISTINCT j.* 
        FROM jobs j
        JOIN job_skills js ON j.Job_ID = js.Job_ID
        JOIN user_skills us ON js.Skill_ID = us.Skill_ID
        WHERE us.User_ID = ?
    ";

    $stmt = $con->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($con->error)); // Debugging line
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no user is logged in, show all jobs
    $query = "SELECT * FROM jobs";
    $result = mysqli_query($con, $query);
    if ($result === false) {
        die("Query failed: " . htmlspecialchars($con->error)); // Debugging line
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>1 Side Hustle</title>
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
    <link href="css/custom.css" rel="stylesheet">
</head>
<body>
    <div class="container-xxl bg-white p-0">
      
        <?php include("navbar.php"); ?>

        <!-- Carousel Start -->
        <div class="container-fluid p-0">
            <div class="owl-carousel header-carousel position-relative">
                <div class="owl-carousel-item position-relative">
                    <img class="img-fluid" src="img/carousel-3.jpg" alt="">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(43, 57, 64, .5);">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-10 col-lg-8">
                                    <h1 class="display-3 text-white animated slideInDown mb-4">Find Job That Suits You</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="owl-carousel-item position-relative">
                    <img class="img-fluid" src="img/carousel-2.jpg" alt="">
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(43, 57, 64, .5);">
                        <div class="container">
                            <div class="row justify-content-start">
                                <div class="col-10 col-lg-8">
                                    <h1 class="display-3 text-white animated slideInDown mb-4">Flexi Jobs With Us</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Name Start -->
        <div style="padding-top: 4%; padding-bottom: 4%;">
            <h1 style="text-align:center; color:#D96127; font-size: 60px;">Welcome to </h1>
            <h1 style="text-align: center; color: #FE7A36; font-size: 50px;"><b>Side Hustle</b></h1>
        </div>

        <!-- Content Start-->
        <section id="content">
            <div class="container px-4 py-5">
                <h2 class="text-center">Top 10 Favor Careers with Us !</h2>
                <div class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-3">
                    <div class="col">
                        <a href="job-list_1.php?search=cashier" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/cashier.jpg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Cashier</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=barista" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/barista.jpg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Barista</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=housekeeping" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/housekeeping.jpg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Housekeeping</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=catsitter" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/catsitter.webp); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Cat Sitter</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=shopper" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/shoppers.jpeg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Personal Shoppers</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=tutor" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/tutor.jpg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite">Tutor</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=lawnmower" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/lawnmower.jpg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Lawn Mower</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=nanny" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/nanny.jpg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Nanny</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <a href="job-list_1.php?search=waiter" class="text-decoration-none">
                            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/waiter.jpg); background-size: cover;">
                                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Waiter</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div><br>
            </div>
        </section>

          <!-- About Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-4 wow fadeIn" data-wow-delay="0.1s">
                        <div class="row g-0 about-bg rounded overflow-hidden">
                            <div class="col-3 text-end">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 wow fadeIn" data-wow-delay="0.3s">
                        <h1 class="mb-4">We Help To Get The Job Best Fit You</h1>
                        <p class="mb-4">Are you looking to unlock your potential, boost your income, and explore new horizons?</p>
                        <p class="mb-4">SideHustle is your all-in-one platform for discovering exciting part-time opportunities that fit seamlessly into your lifestyle.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job Listing Section -->
        <div class="container-xxl py-5">
            <div class="container">
                <h1 class="text-center mb-5">Job Listings</h1><br>
                <h2 class="text-center mb-1">Browse Your Preferred Job Here</h2><br>
                <div class="row">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="col-lg-4 col-md-6 mb-4">';
                            echo '<div class="job-item p-4 mb-4">';
                            echo '<h5>' . htmlspecialchars($row['Title']) . '</h5>';
                            echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['Description']) . '</p>';
                            echo '<p><strong>Location:</strong> ' . htmlspecialchars($row['Location']) . '</p>';
                            echo '<p><strong>Salary:</strong> RM ' . htmlspecialchars($row['Salary']) . ' per ' . $row['SalaryUnit'] . '</p>';
                            echo '<p class="text-truncate me-0"><i class="fa fa-info-circle text-primary me-2"></i> ' . $row['WorkDate'] . ' (' . $row['Duration'] . ' days)</p>';
                            echo '<span><button class="btn btn-orange details-btn" data-job-id="' . htmlspecialchars($row['Job_ID']) . '" data-title="' . htmlspecialchars($row['Title']) . '" data-description="' . htmlspecialchars($row['Description']) . '" data-location="' . htmlspecialchars($row['Location']) . '" data-salary="' . htmlspecialchars($row['Salary']) . '">View Details</button></span>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No jobs found matching your skills.</p>';
                    }
                    ?>
                </div>
            </div>
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
                        
                        <!-- Add application form -->
                        <form id="application-form" action="submit_application.php" method="POST" enctype="multipart/form-data" style="display:none;">
                            <input type="hidden" id="job-id-input" name="job-id">
                            <div class="mb-3">
                                <label for="full-name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full-name" name="full-name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="resume" class="form-label">Resume (PDF)</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            </div>
                            <button type="submit" class="btn btn-orange">Submit Application</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <span><button class="btn btn-orange details-btn" data-job-id="' . htmlspecialchars($row['Job_ID']) . '" data-title="' . htmlspecialchars($row['Title']) . '" data-description="' . htmlspecialchars($row['Description']) . '" data-location="' . htmlspecialchars($row['Location']) . '" data-salary="' . htmlspecialchars($row['Salary']) . '">View Details</button></span>
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

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="lib/wow/wow.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/waypoints/waypoints.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="js/main.js"></script>
        <script>
            //job details modal
            $(document).ready(function() {
                $('.details-btn').click(function() {
                    var jobId = $(this).data('job-id');
                    var title = $(this).data('title');
                    var description = $(this).data('description');
                    var location = $(this).data('location');
                    var salary = $(this).data('salary');

                    $('#modal-title').text(title);
                    $('#modal-description').text('Description: ' + description);
                    $('#modal-location').text('Location: ' + location);
                    $('#modal-salary').text('Salary: RM ' + salary);

                    $('#details-modal').modal('show');
                });

                $('#apply-now-btn').click(function () {
                    var isLoggedIn = $(this).data('logged-in') === true || $(this).data('logged-in') === 'true';

                    if (isLoggedIn) {
                        // Show application form
                        $('#application-form').show();
                        $(this).hide();
                        
                        // Set the job ID in the hidden field
                        var jobId = $(this).closest('.modal-content').find('[data-job-id]').data('job-id');
                        $('#job-id-input').val(jobId);
                    } else {
                        if (confirm('You need to log in first to apply. Click OK to proceed to login page.')) {
                            window.location.href = 'login.php';
                        }
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
                        success: function(response) {
                            var result = JSON.parse(response);
                            if(result.status === 'success') {
                                alert(result.message);
                                $('#details-modal').modal('hide');
                            } else {
                                alert(result.message);
                            }
                        },
                        error: function() {
                            alert('Error submitting application. Please try again.');
                        }
                    });
                });
            });
        </script>
    </div>
</body>
</html>
<?php
mysqli_close($con);
?>