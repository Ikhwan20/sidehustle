<?php
session_start();
include("connection.php");
include("functions.php");

// Debug function to log errors
function debug_to_file($message) {
    file_put_contents('edit_job_debug.log', date('Y-m-d H:i:s') . ': ' . $message . "\n", FILE_APPEND);
}

// Redirect if not logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: employer_login.php");
    die;
}

// Initialize variables
$error_message = '';
$job = [];
$job_id = 0;

try {
    // Check if job_id is provided
    if (isset($_GET['job_id'])) {
        $job_id = (int)$_GET['job_id'];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Get all form fields with validation and default values
            $title = $_POST['title'] ?? '';
            $company = $_POST['company'] ?? '';
            $description = $_POST['description'] ?? '';
            $location = $_POST['location'] ?? '';
            $job_requirements = $_POST['job_requirements'] ?? '';
            $minSalary = !empty($_POST['minSalary']) ? (float)$_POST['minSalary'] : 0;
            $maxSalary = !empty($_POST['maxSalary']) ? (float)$_POST['maxSalary'] : 0;
            $salary = $minSalary; // For backwards compatibility
            $salaryUnit = $_POST['salaryUnit'] ?? 'month';
            $duration = $_POST['duration'] ?? '';
            $jobType = $_POST['jobType'] ?? '';
            $remoteOption = $_POST['remoteOption'] ?? '';
            $skills = $_POST['skills'] ?? '';
            $work_date = !empty($_POST['work_date']) ? $_POST['work_date'] : date('Y-m-d');
            $industry = $_POST['industry'] ?? '';
            $experienceLevel = $_POST['experienceLevel'] ?? 'Entry';
            $applicationDeadline = !empty($_POST['applicationDeadline']) ? $_POST['applicationDeadline'] : null;
            $employmentStatus = $_POST['employmentStatus'] ?? 'Open';

            // Validate required fields
            if (empty($title) || empty($description) || empty($job_requirements)) {
                $error_message = "Please fill in all required fields.";
            } else {
                // Prepare update query
                $query = "UPDATE jobs SET 
                    Title=?, Company=?, Description=?, JobRequirements=?, 
                    Salary=?, SalaryUnit=?, Duration=?, JobType=?, Industry=?, 
                    ExperienceLevel=?, RemoteOption=?, EmploymentStatus=?, 
                    ApplicationDeadline=?, Location=?, WorkDate=?, Skills=?";
                
                // Add MinSalary and MaxSalary if they exist in the database
                $columnCheckQuery = "SHOW COLUMNS FROM jobs LIKE 'MinSalary'";
                $columnResult = $con->query($columnCheckQuery);
                $params = [];
                $types = "ssssdsssssssssss";
                
                if ($columnResult->num_rows > 0) {
                    $query .= ", MinSalary=?, MaxSalary=?";
                    $types .= "dd";
                    $params = [
                        $title, $company, $description, $job_requirements, 
                        $salary, $salaryUnit, $duration, $jobType, $industry, 
                        $experienceLevel, $remoteOption, $employmentStatus, 
                        $applicationDeadline, $location, $work_date, $skills,
                        $minSalary, $maxSalary
                    ];
                } else {
                    $params = [
                        $title, $company, $description, $job_requirements, 
                        $salary, $salaryUnit, $duration, $jobType, $industry, 
                        $experienceLevel, $remoteOption, $employmentStatus, 
                        $applicationDeadline, $location, $work_date, $skills
                    ];
                }
                
                // Complete the query
                $query .= " WHERE Job_ID=? AND Employer_ID=?";
                $types .= "ii";
                $params[] = $job_id;
                $params[] = $_SESSION['employer_id'];
                
                // Prepare and execute the statement
                $stmt = $con->prepare($query);
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $con->error);
                }
                
                // Create reference array for bind_param
                $refs = [];
                foreach ($params as $key => $value) {
                    $refs[$key] = &$params[$key];
                }
                
                // Dynamic parameter binding
                array_unshift($refs, $types);
                call_user_func_array([$stmt, 'bind_param'], $refs);
                
                // Execute the statement
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                // Handle skills if successful
                if ($stmt->affected_rows > 0 || $stmt->errno === 0) {
                    // First clear old job_skills (safely using prepared statement)
                    $clearStmt = $con->prepare("DELETE FROM job_skills WHERE Job_ID = ?");
                    $clearStmt->bind_param("i", $job_id);
                    $clearStmt->execute();
                    
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
                    exit;
                } else {
                    $error_message = "No changes were made to the job.";
                }
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
            $_SESSION['error_message'] = "Job not found or you don't have permission to edit it.";
            header("Location: employer_dashboard.php");
            exit;
        }
    } else {
        // No job_id provided
        $_SESSION['error_message'] = "No job selected for editing.";
        header("Location: employer_dashboard.php");
        exit;
    }
} catch (Exception $e) {
    // Log the error
    debug_to_file("Error: " . $e->getMessage());
    $error_message = "An error occurred: " . $e->getMessage();
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
            <button class="navbar-toggler me-4" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ms-auto p-4 p-lg-0">
                    <li class="nav-item">
                        <span class="nav-link" style="cursor: pointer; color: #FE7A36;">Welcome, <?php echo htmlspecialchars($_SESSION['employer_username']); ?></span>
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
            <h1 class="text-center mb-5">Edit Job</h1>
            
            <?php if(!empty($error_message)): ?>
                <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="row g-4">
                <div class="col-lg-2"></div>
                <div class="col-lg-8">
                    <div class="bg-light rounded p-4 p-lg-5">
                        <form method="POST" action="edit_job.php?job_id=<?php echo $job_id; ?>">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="title" class="form-label">Job Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($job['Title'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="company" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="company" name="company" 
                                           value="<?php echo htmlspecialchars($job['Company'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($job['Location'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="work_date" class="form-label">Work Start Date</label>
                                    <input type="date" class="form-control" id="work_date" name="work_date" 
                                           value="<?php echo htmlspecialchars($job['WorkDate'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="jobType" class="form-label">Job Type</label>
                                    <select class="form-select" id="jobType" name="jobType" required>
                                        <option value="Full-Time" <?php echo ($job['JobType'] ?? '') == 'Full-Time' ? 'selected' : ''; ?>>Full-Time</option>
                                        <option value="Part-Time" <?php echo ($job['JobType'] ?? '') == 'Part-Time' ? 'selected' : ''; ?>>Part-Time</option>
                                        <option value="Contract" <?php echo ($job['JobType'] ?? '') == 'Contract' ? 'selected' : ''; ?>>Contract</option>
                                        <option value="Internship" <?php echo ($job['JobType'] ?? '') == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                                        <option value="Freelance" <?php echo ($job['JobType'] ?? '') == 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="remoteOption" class="form-label">Remote Option</label>
                                    <select class="form-select" id="remoteOption" name="remoteOption" required>
                                        <option value="On-Site" <?php echo ($job['RemoteOption'] ?? '') == 'On-Site' ? 'selected' : ''; ?>>On-Site</option>
                                        <option value="Remote" <?php echo ($job['RemoteOption'] ?? '') == 'Remote' ? 'selected' : ''; ?>>Remote</option>
                                        <option value="Hybrid" <?php echo ($job['RemoteOption'] ?? '') == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="minSalary" class="form-label">Minimum Salary</label>
                                    <input type="number" class="form-control" id="minSalary" name="minSalary" step="0.01"
                                           value="<?php echo htmlspecialchars($job['MinSalary'] ?? $job['Salary'] ?? '0'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="maxSalary" class="form-label">Maximum Salary</label>
                                    <input type="number" class="form-control" id="maxSalary" name="maxSalary" step="0.01"
                                           value="<?php echo htmlspecialchars($job['MaxSalary'] ?? $job['Salary'] ?? '0'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="salaryUnit" class="form-label">Salary Unit</label>
                                    <select class="form-select" id="salaryUnit" name="salaryUnit">
                                        <option value="hour" <?php echo ($job['SalaryUnit'] ?? '') == 'hour' ? 'selected' : ''; ?>>Hourly</option>
                                        <option value="day" <?php echo ($job['SalaryUnit'] ?? '') == 'day' ? 'selected' : ''; ?>>Daily</option>
                                        <option value="month" <?php echo ($job['SalaryUnit'] ?? '') == 'month' ? 'selected' : ''; ?>>Monthly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="duration" class="form-label">Duration (months)</label>
                                    <input type="text" class="form-control" id="duration" name="duration"
                                           value="<?php echo htmlspecialchars($job['Duration'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="experienceLevel" class="form-label">Experience Level</label>
                                    <select class="form-select" id="experienceLevel" name="experienceLevel">
                                        <option value="Entry" <?php echo ($job['ExperienceLevel'] ?? '') == 'Entry' ? 'selected' : ''; ?>>Entry</option>
                                        <option value="Mid" <?php echo ($job['ExperienceLevel'] ?? '') == 'Mid' ? 'selected' : ''; ?>>Mid</option>
                                        <option value="Senior" <?php echo ($job['ExperienceLevel'] ?? '') == 'Senior' ? 'selected' : ''; ?>>Senior</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="employmentStatus" class="form-label">Status</label>
                                    <select class="form-select" id="employmentStatus" name="employmentStatus">
                                        <option value="Open" <?php echo ($job['EmploymentStatus'] ?? '') == 'Open' ? 'selected' : ''; ?>>Open</option>
                                        <option value="Closed" <?php echo ($job['EmploymentStatus'] ?? '') == 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                        <option value="On-Hold" <?php echo ($job['EmploymentStatus'] ?? '') == 'On-Hold' ? 'selected' : ''; ?>>On-Hold</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="industry" class="form-label">Industry</label>
                                    <input type="text" class="form-control" id="industry" name="industry"
                                           value="<?php echo htmlspecialchars($job['Industry'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="applicationDeadline" class="form-label">Application Deadline</label>
                                    <input type="date" class="form-control" id="applicationDeadline" name="applicationDeadline"
                                           value="<?php echo htmlspecialchars($job['ApplicationDeadline'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Job Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($job['Description'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="job_requirements" class="form-label">Job Requirements</label>
                                <textarea class="form-control" id="job_requirements" name="job_requirements" rows="4" required><?php echo htmlspecialchars($job['JobRequirements'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="skills" class="form-label">Skills (comma-separated)</label>
                                <input type="text" class="form-control" id="skills" name="skills"
                                       value="<?php echo htmlspecialchars($job['Skills'] ?? ''); ?>">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4 py-2">Update Job</button>
                                <a href="employer_dashboard.php" class="btn btn-secondary px-4 py-2 ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-2"></div>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Set min date for date fields that don't have a value yet
    const dateFields = ['work_date', 'applicationDeadline'];
    const today = new Date().toISOString().split('T')[0];
    
    dateFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && !field.value) {
            field.min = today;
        }
    });
});
</script>
</body>
</html>