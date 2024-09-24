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
    
    // Prepare the statement to unarchive the user
    $stmt = $conn->prepare("UPDATE users SET is_archived = 0 WHERE id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        // Check if any row was updated
        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = 'The account has been unarchived.';
        } else {
            $_SESSION['error'] = 'No user found with that ID or the user is already active.';
        }
    } else {
        $_SESSION['error'] = 'Error unarchiving user: ' . $conn->error;
    }
    
    $stmt->close(); // Close the statement
} else {
    $_SESSION['error'] = 'No user ID provided.';
}

// Redirect back to the archive page
header("Location: ../../admin/archive.php");
exit();
?>