<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page (or any other page)
header("Location: ../../auth/login.php"); // Change 'login.php' to your desired page
exit();
?>
