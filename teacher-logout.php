<?php
session_start();

// Destroy the session
session_unset(); // Clear session variables
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: login.php");
exit();

