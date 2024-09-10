<?php
// Start the session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to index.php
$_SESSION['success'] = 'Succesfully Logout.';
  header("Location: /school-management/login.php");
  exit();
?>