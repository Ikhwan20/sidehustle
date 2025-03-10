<?php
require 'connection.php';

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $stmt = $con->prepare("DELETE FROM users WHERE User_ID = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "Invalid request.";
}
?>
