<?php
session_start();
include("connection.php");
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
      

         <!-- Navbar Start -->
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
                        <li class="nav-item">
                            <a href="index.php" class="nav-link active">Home</a>
                        </li>
                        <li class="nav-item">
                            <a href="about.html" class="nav-link">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="job-list_1.php" class="nav-link">Job</a>
                        </li>
                        <li class="nav-item">
                            <a href="contact.html" class="nav-link">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a href="login.php" class="nav-link">Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a href="employer_login.php" class="btn rounded-0 py-4 px-lg-5 d-none d-lg-block" style="color: white; background-color: #FE7A36;">Employer<i class="fa fa-arrow-right ms-3"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

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
                <h2 class="text-center">Various Jobs Opportunities with Us !</h2>
                <div class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-3">
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/cashier.jpg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Cashier</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/barista.jpg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Barista</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/housekeeping.jpg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Housekeeping</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/catsitter.webp); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Cat Sitter</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/shoppers.jpeg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Personal Shoppers</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/tutor.jpg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite">Tutor</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/lawnmower.jpg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Lawn Mower</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/nanny.jpg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Nanny</h3>
                              </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg" style="background-image: url(img/waiter.jpg); background-size: cover;">
                            <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                                <h3 class="pt-5 mt-5 mb-4 display-6 lh-md fw-bold fs-3" style="color: antiquewhite;">Waiter</h3>
                              </div>
                        </div>
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
                <h2 class="text-center mb-1">Browse Your Prefered Job Here</h2><br>
                <div class="row">
                    <?php
                    $query = "SELECT * FROM jobs";
                    $result = mysqli_query($con, $query);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="col-lg-4 col-md-6 mb-4">';
                            echo '<div class="job-item p-4 mb-4">';
                            echo '<h5>' . htmlspecialchars($row['Title']) . '</h5>';
                            echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['Description']) . '</p>';
                            echo '<p><strong>Location:</strong> ' . htmlspecialchars($row['Location']) . '</p>';
                            echo '<p><strong>Salary:</strong> RM ' . htmlspecialchars($row['Salary']) . '</p>';
                            echo '<p class="text-truncate me-0"><i class="fa fa-info-circle text-primary me-2"></i> ' . $row['WorkDate'] . ' (' . $row['Duration'] . ' days)</p>';
                            echo '<span><button class="btn btn-orange details-btn" data-job-id="' . htmlspecialchars($row['Job_ID']) . '" data-title="' . htmlspecialchars($row['Title']) . '" data-description="' . htmlspecialchars($row['Description']) . '" data-location="' . htmlspecialchars($row['Location']) . '" data-salary="' . htmlspecialchars($row['Salary']) . '">View Details</button></span>';
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
                    </div>
                    <div class="modal-footer">
                        <button id="apply-now-btn" class="btn btn-orange">Apply Now</button>
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

                //apply button -> bring to login pg
                $('#apply-now-btn').click(function() {
                    var isLoggedIn = false;

                    if (isLoggedIn) {
                        alert('Apply now functionality goes here.');
                    } else {
                        // Prompt user to log in first
                        if (confirm('You need to log in first to apply. Click OK to proceed to login page.')) {
                            window.location.href = 'login.php';
                        }
                    }
                });
            });
        </script>
    </div>
</body>
</html>
<?php
mysqli_close($con);
?>