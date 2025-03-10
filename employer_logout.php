<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to employer login page
header("Location: employer_login.php");
exit;
?>
