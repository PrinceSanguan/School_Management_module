<?php
session_start(); // Start the session

// Include database connection
include 'db_connect.php';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract and sanitize form data
    $taskId = intval($_POST['id']); // Ensure the ID is an integer

    // Prepare and execute SQL query to delete the task
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $taskId);

    if ($stmt->execute()) {
        // Redirect with a success message
        header('Location: task.php?status=deleted');
        exit;
    } else {
        // Redirect with an error message
        header('Location: task.php?status=error');
        exit;
    }
}
?>
