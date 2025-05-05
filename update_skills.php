<?php
session_start();
include("connection.php");

// Check if the user is logged in
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['User_ID'];

// Check if skills were submitted
if (isset($_POST['skills']) && !empty($_POST['skills'])) {
    // Decode the JSON string from Tagify
    $skills_json = $_POST['skills'];
    $skills = json_decode($skills_json, true);
    
    // Begin transaction
    $con->begin_transaction();
    
    try {
        // First, delete all existing skills for this user
        $delete_query = "DELETE FROM user_skills WHERE User_ID = ?";
        $delete_stmt = $con->prepare($delete_query);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        
        // Then add each new skill
        foreach ($skills as $skill_item) {
            $skill_name = trim($skill_item['value']);
            
            // Check if skill already exists in skills table
            $check_query = "SELECT Skill_ID FROM skills WHERE Skill_Name = ?";
            $check_stmt = $con->prepare($check_query);
            $check_stmt->bind_param("s", $skill_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Skill exists, get its ID
                $skill_row = $check_result->fetch_assoc();
                $skill_id = $skill_row['Skill_ID'];
            } else {
                // Skill doesn't exist, insert it
                $insert_skill_query = "INSERT INTO skills (Skill_Name) VALUES (?)";
                $insert_skill_stmt = $con->prepare($insert_skill_query);
                $insert_skill_stmt->bind_param("s", $skill_name);
                $insert_skill_stmt->execute();
                $skill_id = $con->insert_id;
            }
            
            // Now associate the user with this skill
            $insert_user_skill_query = "INSERT INTO user_skills (User_ID, Skill_ID) VALUES (?, ?)";
            $insert_user_skill_stmt = $con->prepare($insert_user_skill_query);
            $insert_user_skill_stmt->bind_param("ii", $user_id, $skill_id);
            $insert_user_skill_stmt->execute();
        }
        
        // Commit transaction
        $con->commit();
        $_SESSION['message'] = "Skills updated successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();
        $_SESSION['error'] = "Error updating skills: " . $e->getMessage();
    }
} else {
    // If no skills were submitted, remove all skills for this user
    $delete_query = "DELETE FROM user_skills WHERE User_ID = ?";
    $delete_stmt = $con->prepare($delete_query);
    $delete_stmt->bind_param("i", $user_id);
    $delete_stmt->execute();
    $_SESSION['message'] = "All skills have been removed.";
}

// Close the database connection
$con->close();

// Redirect back to the dashboard
header("Location: dashboard.php");
exit;
?>