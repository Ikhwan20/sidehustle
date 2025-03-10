<?php
require 'connection.php';

if (isset($_GET['id'])) {
    $job_id = intval($_GET['id']);
    $query = "DELETE FROM jobs WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error deleting job.";
    }
} else {
    echo "Invalid request.";
}
?>
