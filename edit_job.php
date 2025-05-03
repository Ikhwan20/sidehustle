<?php
session_start();
include("connection.php");
include("functions.php");

if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $jobTitle = $_POST['jobTitle'];
        $companyName = $_POST['companyName'];
        $jobDescription = $_POST['jobDescription'];
        $jobRequirements = $_POST['jobRequirements'];
        $salary = $_POST['salary'];
        $salaryUnit = $_POST['salaryUnit'];
        $duration = $_POST['duration'];
        $jobType = $_POST['jobType'];
        $industry = $_POST['industry'];
        $experienceLevel = $_POST['experienceLevel'];
        $remoteOption = $_POST['remoteOption'];
        $employmentStatus = $_POST['employmentStatus'];
        $applicationDeadline = $_POST['applicationDeadline'];
        $location = $_POST['location'];
        $date = $_POST['date'];
        $skills = $_POST['skills'];

        if (!empty($jobTitle) && !empty($jobDescription) && !empty($jobRequirements)) {
            $query = "UPDATE jobs SET 
                Title=?, Company=?, Description=?, JobRequirements=?, Salary=?, SalaryUnit=?, Duration=?, 
                JobType=?, Industry=?, ExperienceLevel=?, RemoteOption=?, EmploymentStatus=?, 
                ApplicationDeadline=?, Location=?, WorkDate=?, Skills=? 
                WHERE Job_ID=? AND Employer_ID=?";

            $stmt = $con->prepare($query);
            $stmt->bind_param(
                "ssssdsisssssssssii",
                $jobTitle, $companyName, $jobDescription, $jobRequirements, $salary, $salaryUnit, $duration,
                $jobType, $industry, $experienceLevel, $remoteOption, $employmentStatus,
                $applicationDeadline, $location, $date, $skills, $job_id, $_SESSION['employer_id']
            );

            if ($stmt->execute()) {
                // Clear old job_skills
                $con->query("DELETE FROM job_skills WHERE Job_ID = $job_id");

                // Process skills
                $skillList = explode(',', $skills);
                foreach ($skillList as $skillName) {
                    $skillName = trim($skillName);
                    if (empty($skillName)) continue;

                    $skillQuery = "SELECT Skill_ID FROM skills WHERE Skill_Name = ?";
                    $skillStmt = $con->prepare($skillQuery);
                    $skillStmt->bind_param("s", $skillName);
                    $skillStmt->execute();
                    $skillResult = $skillStmt->get_result();

                    if ($skillResult->num_rows > 0) {
                        $row = $skillResult->fetch_assoc();
                        $skillId = $row['Skill_ID'];
                    } else {
                        $insertSkill = "INSERT INTO skills (Skill_Name) VALUES (?)";
                        $insertStmt = $con->prepare($insertSkill);
                        $insertStmt->bind_param("s", $skillName);
                        $insertStmt->execute();
                        $skillId = $insertStmt->insert_id;
                    }

                    $linkQuery = "INSERT INTO job_skills (Job_ID, Skill_ID) VALUES (?, ?)";
                    $linkStmt = $con->prepare($linkQuery);
                    $linkStmt->bind_param("ii", $job_id, $skillId);
                    $linkStmt->execute();
                }

                $_SESSION['success_message'] = "Job updated successfully.";
                header("Location: employer_dashboard.php");
                die;
            } else {
                echo "<script>alert('Error updating job.');</script>";
            }
        } else {
            echo "<script>alert('Please fill in all required fields.');</script>";
        }
    }

    $query = "SELECT * FROM jobs WHERE Job_ID='$job_id' AND Employer_ID='{$_SESSION['employer_id']}' LIMIT 1";
    $result = mysqli_query($con, $query);
    $job = mysqli_fetch_assoc($result);
} else {
    header("Location: employer_dashboard.php");
    die;
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Job</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-xxl bg-white p-0">
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <div class="container-fluid">
            <a href="index.html" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
                <h1 class="m-0" style="color: #FE7A36;">Side Hustle</h1>
            </a>
            <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto p-4 p-lg-0">
                    <li class="nav-item">
                        <span class="nav-link" style="cursor: pointer; color: #FE7A36;">Welcome,  <?php echo$_SESSION['employer_username']; ?></span>
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

    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Edit Job</h1>
            <div class="row g-4">
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
                <div class="col-md-6">
                    <div class="wow fadeInUp" data-wow-delay="0.5s">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="jobTitle" class="form-control" placeholder="Job Title" value="<?php echo $job['Title']; ?>" required>
                                        <label for="jobTitle">Job Title</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input id="text" type="text" name="companyName" class="form-control" placeholder="Company Name" required>
                                        <label for="companyName">Company Name</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="jobDescription" class="form-control" placeholder="Job Description" style="height: 150px;" required><?php echo $job['Description']; ?></textarea>
                                        <label for="jobDescription">Job Description</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="jobRequirements" class="form-control" placeholder="Job Requirements" style="height: 150px;" required><?php echo $job['JobRequirements']; ?></textarea>
                                        <label for="jobRequirements">Job Requirements</label>
                                    </div>
                                </div>
                                <!-- Salary -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="salary" class="form-control" placeholder="Salary" value="<?php echo $job['Salary']; ?>" required>
                                        <label for="salary">Salary</label>
                                    </div>
                                </div>

                                <!-- Salary Unit -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-control" name="salaryUnit" required>
                                            <option value="monthly" <?php if($job['SalaryUnit'] == 'monthly') echo 'selected'; ?>>Monthly</option>
                                            <option value="hourly" <?php if($job['SalaryUnit'] == 'hourly') echo 'selected'; ?>>Hourly</option>
                                        </select>
                                        <label for="salaryUnit">Salary Unit</label>
                                    </div>
                                </div>

                                <!-- Duration -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="duration" class="form-control" placeholder="Duration" value="<?php echo $job['Duration']; ?>" required>
                                        <label for="duration">Duration</label>
                                    </div>
                                </div>

                                <!-- Job Type -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-control" name="jobType" required>
                                            <option value="part-time" <?php if($job['JobType'] == 'part-time') echo 'selected'; ?>>Part-time</option>
                                            <option value="full-time" <?php if($job['JobType'] == 'full-time') echo 'selected'; ?>>Full-time</option>
                                        </select>
                                        <label for="jobType">Job Type</label>
                                    </div>
                                </div>

                                <!-- Industry -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="industry" class="form-control" placeholder="Industry" value="<?php echo $job['Industry']; ?>" required>
                                        <label for="industry">Industry</label>
                                    </div>
                                </div>

                                <!-- Experience Level -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-control" name="experienceLevel" required>
                                            <option value="entry" <?php if($job['ExperienceLevel'] == 'entry') echo 'selected'; ?>>Entry</option>
                                            <option value="intermediate" <?php if($job['ExperienceLevel'] == 'intermediate') echo 'selected'; ?>>Intermediate</option>
                                            <option value="senior" <?php if($job['ExperienceLevel'] == 'senior') echo 'selected'; ?>>Senior</option>
                                        </select>
                                        <label for="experienceLevel">Experience Level</label>
                                    </div>
                                </div>

                                <!-- Remote Option -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-control" name="remoteOption" required>
                                            <option value="yes" <?php if($job['RemoteOption'] == 'yes') echo 'selected'; ?>>Yes</option>
                                            <option value="no" <?php if($job['RemoteOption'] == 'no') echo 'selected'; ?>>No</option>
                                        </select>
                                        <label for="remoteOption">Remote Option</label>
                                    </div>
                                </div>

                                <!-- Employment Status -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="employmentStatus" class="form-control" placeholder="Employment Status" value="<?php echo $job['EmploymentStatus']; ?>" required>
                                        <label for="employmentStatus">Employment Status</label>
                                    </div>
                                </div>

                                <!-- Application Deadline -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" name="applicationDeadline" class="form-control" value="<?php echo $job['ApplicationDeadline']; ?>" required>
                                        <label for="applicationDeadline">Application Deadline</label>
                                    </div>
                                </div>

                                <!-- Work Date -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" name="date" class="form-control" value="<?php echo $job['WorkDate']; ?>" required>
                                        <label for="date">Work Start Date</label>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="location" class="form-control" placeholder="Location" value="<?php echo $job['Location']; ?>" required>
                                        <label for="location">Location</label>
                                    </div>
                                </div>

                                <!-- Skills -->
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" name="skills" class="form-control" placeholder="Skills" value="<?php echo $job['Skills']; ?>" required>
                                        <label for="skills">Skills (comma-separated)</label>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="col-12">
                                    <button class="btn w-100 py-3" type="submit" style="background-color: #FE7A36; color: white;">Update Job</button>
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

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
