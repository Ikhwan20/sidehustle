<?php
include("connection.php");

$result = mysqli_query($con, "SELECT Skill_Name FROM skills ORDER BY Skill_Name ASC");
$skills = [];

while ($row = mysqli_fetch_assoc($result)) {
    $skills[] = $row['Skill_Name'];
}

header('Content-Type: application/json');
echo json_encode($skills);
