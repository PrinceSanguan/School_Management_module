<?php
// Start the session
session_start();

// Check if the user is an admin or teacher
if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'teacher')) {
    // Destroy all session data
    session_unset(); // Unsets all session variables
    session_destroy(); // Destroys the session

    // Redirect to the login page or homepage
    header('Location: login.php');
    exit();
} else {
    // If the user is not an admin or teacher, redirect to an error page or homepage
    header('Location: login.php'); // Adjust the URL as needed
    exit();
}
?>
