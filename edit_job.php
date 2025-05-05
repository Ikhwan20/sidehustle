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
        // Get all form fields
        $title = $_POST['title'];
        $company = $_POST['company'];
        $description = $_POST['description'];
        $location = $_POST['location'];
        $jobRequirements = $_POST['job_requirements'];
        $minSalary = $_POST['minSalary'] ?? 0;
        $maxSalary = $_POST['maxSalary'] ?? 0;
        $salary = $minSalary; // For backwards compatibility
        $salaryUnit = $_POST['salaryUnit'];
        $duration = $_POST['duration'];
        $jobType = $_POST['jobType'];
        $remoteOption = $_POST['remoteOption'];
        $skills = $_POST['skills'];
        $workDate = $_POST['work_date'] ?? date('Y-m-d');
        $industry = $_POST['industry'] ?? '';
        $experienceLevel = $_POST['experienceLevel'] ?? 'Entry';
        $applicationDeadline = $_POST['applicationDeadline'] ?? null;
        $employmentStatus = $_POST['employmentStatus'] ?? 'Open';

        if (!empty($title) && !empty($description) && !empty($jobRequirements)) {
            $query = "UPDATE jobs SET 
                Title=?, Company=?, Description=?, JobRequirements=?, MinSalary=?, MaxSalary=?, Salary=?, SalaryUnit=?, Duration=?, 
                JobType=?, Industry=?, ExperienceLevel=?, RemoteOption=?, EmploymentStatus=?, 
                ApplicationDeadline=?, Location=?, WorkDate=?, Skills=? 
                WHERE Job_ID=? AND Employer_ID=?";

            $stmt = $con->prepare($query);
            $stmt->bind_param(
                "ssssdddsssssssssssi",
                $title,
                $company,
                $description,
                $jobRequirements,
                $minSalary,
                $maxSalary,
                $salary,
                $salaryUnit,
                $duration,
                $jobType,
                $industry,
                $experienceLevel,
                $remoteOption,
                $employmentStatus,
                $applicationDeadline,
                $location,
                $workDate,
                $skills,
                $job_id,
                $_SESSION['employer_id']
            );

            if ($stmt->execute()) {
                // Clear old job_skills
                $con->query("DELETE FROM job_skills WHERE Job_ID = $job_id");

                // Process skills
                $skillList = explode(',', $skills);
                foreach ($skillList as $skillName) {
                    $skillName = trim($skillName);
                    if (empty($skillName)) continue;

                    // Check if skill exists
                    $skillQuery = "SELECT Skill_ID FROM skills WHERE Skill_Name = ?";
                    $skillStmt = $con->prepare($skillQuery);
                    $skillStmt->bind_param("s", $skillName);
                    $skillStmt->execute();
                    $skillResult = $skillStmt->get_result();

                    if ($skillResult->num_rows > 0) {
                        $row = $skillResult->fetch_assoc();
                        $skillId = $row['Skill_ID'];
                    } else {
                        // Insert new skill
                        $insertSkill = "INSERT INTO skills (Skill_Name) VALUES (?)";
                        $insertStmt = $con->prepare($insertSkill);
                        $insertStmt->bind_param("s", $skillName);
                        $insertStmt->execute();
                        $skillId = $insertStmt->insert_id;
                    }

                    // Link skill to job
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

    // Fetch job data
    $query = "SELECT * FROM jobs WHERE Job_ID=? AND Employer_ID=? LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $job_id, $_SESSION['employer_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
    } else {
        // Job not found or doesn't belong to this employer
        header("Location: employer_dashboard.php");
        die;
    }
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
                        <span class="nav-link" style="cursor: pointer; color: #FE7A36;">Welcome, <?php echo $_SESSION['employer_username']; ?></span>
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
                        <form action="edit_job.php?job_id=<?= $job_id ?>" method="POST">
                            <label for="title">Job Title:</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($job['Title']) ?>" required><br>

                            <label for="company">Company Name:</label>
                            <input type="text" name="company" value="<?= htmlspecialchars($job['Company']) ?>" required><br>
                            
                            <label for="location">Location:</label>
                            <input type="text" name="location" value="<?= htmlspecialchars($job['Location']) ?>" required><br>

                            <label for="jobType">Job Type:</label>
                            <select name="jobType" required>
                                <option value="Full-Time" <?= $job['JobType'] == 'Full-Time' ? 'selected' : '' ?>>Full-Time</option>
                                <option value="Part-Time" <?= $job['JobType'] == 'Part-Time' ? 'selected' : '' ?>>Part-Time</option>
                                <option value="Contract" <?= $job['JobType'] == 'Contract' ? 'selected' : '' ?>>Contract</option>
                                <option value="Internship" <?= $job['JobType'] == 'Internship' ? 'selected' : '' ?>>Internship</option>
                                <option value="Freelance" <?= $job['JobType'] == 'Freelance' ? 'selected' : '' ?>>Freelance</option>
                            </select><br>

                            <label for="duration">Duration (in months):</label>
                            <input type="text" name="duration" value="<?= htmlspecialchars($job['Duration']) ?>"><br>

                            <label for="remoteOption">Remote Option:</label>
                            <select name="remoteOption" required>
                                <option value="On-Site" <?= $job['RemoteOption'] == 'On-Site' ? 'selected' : '' ?>>On-Site</option>
                                <option value="Remote" <?= $job['RemoteOption'] == 'Remote' ? 'selected' : '' ?>>Remote</option>
                                <option value="Hybrid" <?= $job['RemoteOption'] == 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                            </select><br>

                            <label for="minSalary">Minimum Salary:</label>
                            <input type="number" name="minSalary" value="<?= htmlspecialchars($job['MinSalary'] ?? $job['Salary']) ?>"><br>

                            <label for="maxSalary">Maximum Salary:</label>
                            <input type="number" name="maxSalary" value="<?= htmlspecialchars($job['MaxSalary'] ?? $job['Salary']) ?>"><br>

                            <label for="salaryUnit">Salary Unit:</label>
                            <select name="salaryUnit" required>
                                <option value="hour" <?= $job['SalaryUnit'] == 'hour' ? 'selected' : '' ?>>Hourly</option>
                                <option value="day" <?= $job['SalaryUnit'] == 'day' ? 'selected' : '' ?>>Daily</option>
                                <option value="month" <?= $job['SalaryUnit'] == 'month' ? 'selected' : '' ?>>Monthly</option>
                            </select><br>
                            
                            <label for="industry">Industry:</label>
                            <input type="text" name="industry" value="<?= htmlspecialchars($job['Industry']) ?>"><br>
                            
                            <label for="experienceLevel">Experience Level:</label>
                            <select name="experienceLevel">
                                <option value="Entry" <?= $job['ExperienceLevel'] == 'Entry' ? 'selected' : '' ?>>Entry</option>
                                <option value="Mid" <?= $job['ExperienceLevel'] == 'Mid' ? 'selected' : '' ?>>Mid</option>
                                <option value="Senior" <?= $job['ExperienceLevel'] == 'Senior' ? 'selected' : '' ?>>Senior</option>
                            </select><br>
                            
                            <label for="employmentStatus">Employment Status:</label>
                            <select name="employmentStatus">
                                <option value="Open" <?= $job['EmploymentStatus'] == 'Open' ? 'selected' : '' ?>>Open</option>
                                <option value="Closed" <?= $job['EmploymentStatus'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
                                <option value="On-Hold" <?= $job['EmploymentStatus'] == 'On-Hold' ? 'selected' : '' ?>>On-Hold</option>
                            </select><br>
                            
                            <label for="work_date">Work Start Date:</label>
                            <input type="date" name="work_date" value="<?= htmlspecialchars($job['WorkDate']) ?>"><br>
                            
                            <label for="applicationDeadline">Application Deadline:</label>
                            <input type="date" name="applicationDeadline" value="<?= htmlspecialchars($job['ApplicationDeadline']) ?>"><br>

                            <label for="description">Job Description:</label><br>
                            <textarea name="description" rows="6" cols="50" required><?= htmlspecialchars($job['Description']) ?></textarea><br>
                            
                            <label for="job_requirements">Job Requirements:</label><br>
                            <textarea name="job_requirements" rows="6" cols="50" required><?= htmlspecialchars($job['JobRequirements']) ?></textarea><br>

                            <label for="skills">Skills (comma-separated):</label>
                            <input type="text" name="skills" value="<?= htmlspecialchars($job['Skills']) ?>"><br>

                            <input type="submit" value="Update Job" class="btn btn-primary mt-3">
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
<script>
function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
    }
}

// Optional: Set min date for application deadline to today
document.addEventListener('DOMContentLoaded', function() {
    const deadlineInput = document.querySelector('input[name="applicationDeadline"]');
    const workDateInput = document.querySelector('input[name="work_date"]');
    
    if (deadlineInput && workDateInput) {
        const today = new Date().toISOString().split('T')[0];
        if (!workDateInput.value) workDateInput.min = today;
        if (!deadlineInput.value) deadlineInput.min = today;
    }
});
</script>
</body>
</html>