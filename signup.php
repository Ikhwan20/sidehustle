<?php
session_start();

include("connection.php");
include("functions.php");

// Fetch skills
$sql = "SELECT Skill_Name FROM skills";
$result = $con->query($sql);

$skills = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $skills[] = $row['Skill_Name'];
    }
}

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST["Username"]);
    $email = sanitizeInput($_POST["Email"]);
    $password = password_hash(sanitizeInput($_POST["Password"]), PASSWORD_BCRYPT); // Hash password
    $gender = sanitizeInput($_POST["gender"]);
    $age = intval($_POST["age"]);
    $resume = "";
    $profilePic = "";

    // Resume Upload Handling
    if (!empty($_FILES["resume"]["name"])) {
        if ($_FILES["resume"]["error"] !== UPLOAD_ERR_OK) {
            die("Resume upload failed. Error code: " . $_FILES["resume"]["error"]);
        }
    
        $resumeName = basename($_FILES["resume"]["name"]);
        $resumeTmpName = $_FILES["resume"]["tmp_name"];
        $resumeType = $_FILES["resume"]["type"];
        $resumeSize = $_FILES["resume"]["size"];
    
        echo "Temp name: $resumeTmpName<br>";
        echo "Name: $resumeName<br>";
        echo "Size: $resumeSize<br>";
        echo "Type: $resumeType<br>";
    
        if ($resumeSize <= 5 * 1024 * 1024 && $resumeType === "application/pdf") {
            if (!is_dir("uploads")) mkdir("uploads", 0777, true);
            $resume = "uploads/" . $resumeName;
    
            if (!move_uploaded_file($resumeTmpName, $resume)) {
                die("Failed to move uploaded resume. Check permissions.");
            }
        } else {
            die("Invalid resume file. Must be a PDF and under 5MB.");
        }
    }
    

    // Profile Picture Upload Handling
    if (!empty($_FILES["profile-pic"]["name"])) {
        $profilePicName = basename($_FILES["profile-pic"]["name"]);
        $profilePicTmpName = $_FILES["profile-pic"]["tmp_name"];
        $profilePicType = $_FILES["profile-pic"]["type"];
        $profilePicSize = $_FILES["profile-pic"]["size"];

        if ($profilePicSize <= 2 * 1024 * 1024 && in_array($profilePicType, ["image/jpeg", "image/png"])) {
            if (!is_dir("uploads")) mkdir("uploads", 0777);
            $profilePic = "uploads/" . $profilePicName;
            move_uploaded_file($profilePicTmpName, $profilePic);
        } else {
            die("Invalid profile picture. Must be JPG/PNG and under 2MB.");
        }
    }

    // Insert User Data
    $stmt = $con->prepare("INSERT INTO users (Username, Email, Password, Gender, Age, Resume, ProfilePic) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiis", $username, $email, $password, $gender, $age, $resume, $profilePic);
    
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $stmt->close();

        // Insert Skills if provided
        if (!empty($_POST['skills'])) {
            $skills = explode(",", sanitizeInput($_POST['skills']));
            foreach ($skills as $skill) {
                $skill = trim($skill);
                if (!empty($skill)) {
                    // Insert skill if it doesn't exist
                    $skill_stmt = $con->prepare("INSERT INTO skills (Skill_Name) VALUES (?) ON DUPLICATE KEY UPDATE Skill_ID=LAST_INSERT_ID(Skill_ID)");
                    $skill_stmt->bind_param("s", $skill);
                    $skill_stmt->execute();
                    $skill_id = $skill_stmt->insert_id;
                    $skill_stmt->close();

                    // Link user to skill
                    $user_skill_stmt = $con->prepare("INSERT INTO user_skills (User_ID, Skill_ID) VALUES (?, ?)");
                    $user_skill_stmt->bind_param("ii", $user_id, $skill_id);
                    $user_skill_stmt->execute();
                    $user_skill_stmt->close();
                }
            }
        }

        $_SESSION['signup_success'] = true;
        header("Location: login.php");
        exit();
    } else {
        die("Error registering user: " . $stmt->error);
    }
}
?>


<!DOCTYPE html>
<html>
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
        .autocomplete-items { border: 1px solid #ccc; max-height: 150px; overflow-y: auto; position: absolute; z-index: 1000; width: 30%; background: white; }
        .autocomplete-items div { padding: 8px; cursor: pointer; }
        .autocomplete-items div:hover { background-color: #e9e9e9; }
        .autocomplete-active { background-color: #007bff !important; color: #ffffff; }
    </style>
</head>
<body>
<div class="container-xxl bg-white p-0">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center text-center py-0 px-4 px-lg-5">
            <h1 class="m-0" style="color: #FE7A36;">Side Hustle</h1>
        </a>
        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.php" class="nav-item nav-link active">Home</a>
                <a href="about.html" class="nav-item nav-link">About</a>
                <a href="job-list.html" class="nav-item nav-link">Job</a>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
                <a href="./job-form.html" class="btn rounded-0 py-4 px-lg-5 d-none d-lg-block" style="color: white; background-color: #FE7A36;">Employer<i class="fa fa-arrow-right ms-3"></i></a>
            </div>
        </div>
    </nav>

    <!-- Sign Up Form Start -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h2 class="text-center mb-4">Sign Up</h2>
                <form id="signup-form" method="post" action="signup.php" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="form-group mb-3">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="Username" id="Username" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="Email" id="Email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="Password" id="Password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Gender</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" required>
                            <label class="form-check-label" for="genderMale">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" required>
                            <label class="form-check-label" for="genderFemale">Female</label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="age">Age</label>
                        <input type="number" class="form-control" name="age" id="age" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="resume">Upload Resume</label>
                        <input type="file" class="form-control" id="resume" name="resume" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="skills">Skills (optional)</label>
                        <input type="text" id="skill-input" class="form-control" placeholder="Type a skill">
                        <div id="autocomplete-list" class="autocomplete-items"></div>
                        <div id="selected-skills" class="mt-2"></div>
                        <input type="hidden" name="skills" id="skills-hidden">
                    </div>
                    <div class="form-group mb-3">
                        <label for="profile-pic">Profile Picture</label><br>
                        <input type="file" id="profile-pic" name="profile-pic" accept="image/*" onchange="previewProfilePic(event)">
                    </div>
                    <div class="form-group mb-3">
                        <label>Image Preview:</label><br>
                        <img id="image-preview" src="#" alt="Image Preview" style="display: none; max-width: 320px; max-height: 240px;">
                    </div>

                    <div class="form-group mb-3">
                        <button class="btn btn-primary w-100 py-3" type="submit">Sign Up</button>
                    </div>
                </form>
                <div id="signup-message" class="mt-3"></div>
            </div>
        </div>
    </div>
    <!-- Sign Up Form End -->

    <!-- Footer -->
    <div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5 wow fadeIn">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Company</h5>
                    <a class="btn btn-link text-white-50" href="./index.html">Home</a>
                    <a class="btn btn-link text-white-50" href="./job-list.html">Job Lists</a>
                    <a class="btn btn-link text-white-50" href="./job-form.html">Post Job</a>
                    <a class="btn btn-link text-white-50" href="">Terms & Condition</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <a class="btn btn-link text-white-50" href="./index.html">Home</a>
                    <a class="btn btn-link text-white-50" href="./about.html">About Us</a>
                    <a class="btn btn-link text-white-50" href="./contact.html">Contact Us</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-4">Contact Us</h5>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>(00) 123 456</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>SideHustle@gmail.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
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

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/wow/wow.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="js/main.js"></script>

    <script>
        function previewProfilePic(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('image-preview');
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
    </script>

<script>
    $(document).ready(function () {
        let availableSkills = <?php echo json_encode($skills); ?>;
        let selectedSkills = [];

        $('#skill-input').on('input', function () {
            let val = this.value;
            let listDiv = $('#autocomplete-list');
            listDiv.empty();
            if (!val) return;

            availableSkills.forEach(skill => {
                if (skill.toLowerCase().startsWith(val.toLowerCase())) {
                    let item = $('<div>').text(skill);
                    item.click(function () {
                        if (!selectedSkills.includes(skill)) {
                            selectedSkills.push(skill);
                            updateSelectedSkills();
                        }
                        $('#skill-input').val('');
                        listDiv.empty();
                    });
                    listDiv.append(item);
                }
            });
        });

        $('#skill-input').on('keydown', function (e) {
            if (e.keyCode === 13) e.preventDefault();  // Prevent form submission on Enter
        });

        function updateSelectedSkills() {
            let skillContainer = $('#selected-skills');
            skillContainer.empty();
            selectedSkills.forEach(skill => {
                skillContainer.append(`<span class="badge bg-primary m-1">${skill} <span onclick="removeSkill('${skill}')" style="cursor:pointer;">✖</span></span>`);
            });
            $('#skills-hidden').val(selectedSkills.join(','));
        }

        window.removeSkill = function (skill) {
            selectedSkills = selectedSkills.filter(s => s !== skill);
            updateSelectedSkills();
        };
    });
</script>
</body>
</html>