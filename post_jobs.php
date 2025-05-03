<?php
session_start();

include("connection.php");
include("functions.php");

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $workDate = $_POST['work_date'] ?? date('Y-m-d');
    $jobRequirements = $_POST['job_requirements'] ?? '';
    $salary = $_POST['salary'] ?? 0.00;
    $salaryUnit = $_POST['salary_unit'] ?? 'month';
    $duration = $_POST['duration'] ?? 1;
    $jobType = $_POST['job_type'] ?? 'Full-Time';
    $industry = $_POST['industry'] ?? '';
    $experienceLevel = $_POST['experience_level'] ?? 'Entry';
    $remoteOption = $_POST['remote_option'] ?? 'On-Site';
    $applicationDeadline = $_POST['application_deadline'] ?? null;
    $employmentStatus = $_POST['employment_status'] ?? 'Open';
    $skills = $_POST['skills'] ?? '[]'; // Expecting JSON array from frontend

    $employerId = $_SESSION['employer_id'];

    // Store skills JSON in jobs table
    $skillsJson = json_encode(json_decode($skills, true)); // Clean JSON

    $stmt = $con->prepare("INSERT INTO jobs (
        Title, Company, Description, Location, WorkDate, Employer_ID, 
        JobRequirements, Salary, SalaryUnit, Duration, JobType, Industry, 
        ExperienceLevel, RemoteOption, Skills, ApplicationDeadline, EmploymentStatus
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("sssssidssisssssss",
        $title, $company, $description, $location, $workDate, $employerId,
        $jobRequirements, $salary, $salaryUnit, $duration, $jobType, $industry,
        $experienceLevel, $remoteOption, $skillsJson, $applicationDeadline, $employmentStatus
    );

    if ($stmt->execute()) {
        $jobId = $stmt->insert_id;

        // Process and link skills
        $skillsArray = json_decode($skills, true);
        $skillList = is_array($skillsArray) ? $skillsArray : [];

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
            $linkStmt->bind_param("ii", $jobId, $skillId);
            $linkStmt->execute();
        }

        echo json_encode(["success" => true, "job_id" => $jobId]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to post job"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Post a Job</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <style>
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; }
    </style>
</head>
<!-- Tagify JS -->
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>

<script>
document.addEventListener('DOMContentLoaded', async function () {
    // Example predefined skills
    const skillSuggestions = await fetch('skills_list.php').then(res => res.json());

    const input = document.querySelector('#skillsInput');
    const tagify = new Tagify(input, {
        whitelist: skillSuggestions,
        maxTags: 20,
        dropdown: {
            enabled: 0,
            classname: "tags-look",
            maxItems: 10,
            position: "text",
            closeOnSelect: false
        }
    });
});
</script>

<body>
<div class="container-xxl bg-white p-0">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
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
                        <a href="post_jobs.php" class="nav-link active">Post Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="logout-link nav-link" onclick="confirmLogout()">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Job Post Form -->
    <div class="container-xxl py-5">
        <div class="container">
            <h1 class="text-center mb-5 wow fadeInUp" data-wow-delay="0.1s">Post a Job</h1>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="row g-4">
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
                <div class="col-md-6">
                    <div class="wow fadeInUp" data-wow-delay="0.5s">
                        <form id="jobForm" method="POST" action="post_jobs.php">
                            <label for="title">Job Title</label>
                            <input type="text" name="title" required>

                            <label for="company">Company Name</label>
                            <input type="text" name="company" required>

                            <label for="description">Job Description</label>
                            <textarea name="description" required></textarea>

                            <label for="location">Location</label>
                            <input type="text" name="location" required>

                            <label for="work_date">Work Start Date</label>
                            <input type="date" name="work_date">

                            <label for="job_requirements">Job Requirements</label>
                            <textarea name="job_requirements" required></textarea>

                            <label for="salary">Salary (RM)</label>
                            <input type="number" step="0.01" name="salary" required>

                            <label for="salary_unit">Salary Unit</label>
                            <select name="salary_unit">
                                <option value="hour">Hour</option>
                                <option value="day">Day</option>
                                <option value="month" selected>Month</option>
                            </select>

                            <label for="duration">Duration (in months)</label>
                            <input type="number" name="duration" min="1" value="1">

                            <label for="job_type">Job Type</label>
                            <select name="job_type">
                                <option>Full-Time</option>
                                <option>Part-Time</option>
                                <option>Contract</option>
                                <option>Internship</option>
                                <option>Freelance</option>
                            </select>

                            <label for="industry">Industry</label>
                            <input type="text" name="industry">

                            <label for="experience_level">Experience Level</label>
                            <select name="experience_level">
                                <option>Entry</option>
                                <option>Mid</option>
                                <option>Senior</option>
                            </select>

                            <label for="remote_option">Remote Option</label>
                            <select name="remote_option">
                                <option>On-Site</option>
                                <option>Remote</option>
                                <option>Hybrid</option>
                            </select>

                            <label for="skills">Skills (comma-separated)</label>
                            <input type="text" name="skills" placeholder="e.g. PHP, MySQL, Docker">

                            <label for="application_deadline">Application Deadline</label>
                            <input type="date" name="application_deadline">

                            <label for="employment_status">Employment Status</label>
                            <select name="employment_status">
                                <option>Open</option>
                                <option>Closed</option>
                                <option>On-Hold</option>
                            </select>

                            <button type="submit">Post Job</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-3 wow fadeInUp" data-wow-delay="0.1s"></div>
            </div>
        </div>
    </div>
</div>

<!-- Include necessary JS files -->
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "logout.php";
        }
    }
    
    function toggleSubmitButton() {
        const termsCheck = document.getElementById('termsCheck');
        const postJobButton = document.getElementById('postJobButton');
        postJobButton.disabled = !termsCheck.checked;
    }
    
    // Optional: Set min date for application deadline to today
    document.addEventListener('DOMContentLoaded', function() {
        const deadlineInput = document.querySelector('input[name="applicationDeadline"]');
        const workDateInput = document.querySelector('input[name="date"]');
        
        const today = new Date().toISOString().split('T')[0];
        workDateInput.min = today;
        deadlineInput.min = today;
    });
</script>
<script>
        document.getElementById("jobForm").addEventListener("submit", function(e) {
            const skillInput = document.querySelector('input[name="skills"]');
            const skills = skillInput.value.split(',').map(s => s.trim()).filter(s => s);
            skillInput.value = JSON.stringify(skills); // Convert to JSON before submit
        });
    </script>
</body>
</html>