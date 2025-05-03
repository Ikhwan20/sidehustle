<?php
session_start();
include("connection.php");

$user_id = $_SESSION['user_id'];
$skills = trim($_POST['skills']);

$stmt = mysqli_prepare($con, "UPDATE users SET Skills = ? WHERE ID = ?");
mysqli_stmt_bind_param($stmt, "si", $skills, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: dashboard.php");
exit;
