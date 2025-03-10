<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
</head>
<body>
    <div class="container">
        <h1>Job Listings</h1>
        <div class="job-listings">
            <?php
            // Include the PHP file that fetches job listings
            include 'jobs.php';
            include 'config.php';

            // Loop through the job listings and generate HTML code
            foreach ($jobListings as $job) {
                ?>
                <div class="job">
                    <h2><?php echo $job['title']; ?></h2>
                    <p><?php echo $job['description']; ?></p>
                    <p>Location: <?php echo $job['location']; ?></p>
                    <p>Salary: <?php echo $job['salary']; ?></p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</body>
</html>
