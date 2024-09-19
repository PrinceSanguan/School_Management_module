<?php
include "../../database/database.php";
require "../../database/config.php";

// Ensure the user is a teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    die("Unauthorized access");
}

// Ensure the required data is provided
if (isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];  // Get the subject_images.id from the POST request
    $action = $_POST['action'];
    
    // Determine the new status
    $newStatus = $action === 'publish' ? 'publish' : 'unpublish';
    
    // Update query using the subject_images.id
    $query = "UPDATE subject_images
              SET status = ?
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $newStatus, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Status is updated!';
        header("Location: ../../teacher/assign_subject.php");
    } else {
        echo "Error updating status.";
    }

    $stmt->close();
}

$conn->close();
?>