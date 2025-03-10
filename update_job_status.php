<?php
include("connection.php");

// Calculate the date 7 days ago
$seven_days_ago = date('Y-m-d', strtotime('-7 days'));

// Update jobs that have been posted more than 7 days ago
$update_query = "UPDATE jobs SET Active = 0 WHERE Posted_At <= ?";
$stmt = $con->prepare($update_query);
$stmt->bind_param("s", $seven_days_ago);
$stmt->execute();
$stmt->close();

$con->close();
?>