<?php
include "../../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../index.php");
    exit();
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    // Prepare the statement to delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        // Check if any row was deleted
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = 'The account has been permanently deleted.';
        } else {
            $_SESSION['error'] = 'No user found with that ID or the user has already been deleted.';
        }
    } else {
        $_SESSION['error'] = 'Error deleting user: ' . $conn->error;
    }
    
    $stmt->close(); // Close the statement
} else {
    $_SESSION['error'] = 'No user ID provided.';
}

// Redirect back to the archive page
header("Location: ../../admin/archive.php");
exit();
?>