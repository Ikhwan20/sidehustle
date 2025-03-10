<?php
session_start();
include("connection.php");
include("functions.php");

if(!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

if(isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    // Fetch applications for the job
    $query = "SELECT * FROM applications WHERE Job_ID='$job_id'";
    $result = mysqli_query($con, $query);
} else {
    header("Location: employer_dashboard.php");
    die;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>View Applications</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-xxl bg-white p-0">
    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Applications</h1>
            
            <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="list-group">
                    <?php while($application = mysqli_fetch_assoc($result)): ?>
                        <div class="list-group-item">
                            <h5 class="mb-1"><?php echo $application['Applicant_Name']; ?></h5>
                            <p class="mb-1"><?php echo $application['Cover_Letter']; ?></p>
                            <small>Email: <?php echo $application['Applicant_Email']; ?> | Date: <?php echo $application['Application_Date']; ?></small>
                            <p class="mb-1">Resume: <a href="<?php echo $application['Resume']; ?>" target="_blank">View Resume</a></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No applications received yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
