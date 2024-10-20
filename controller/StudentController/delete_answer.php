<?php
// Include the database connection file
include "../../database/database.php"; 
session_start();

// Check if the user is logged in and has the necessary permissions
if (!isset($_SESSION['userId']) || $_SESSION['userRole'] !== 'student') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Validate if the task ID is passed through POST
if (!isset($_POST['task_id'])) {
    $_SESSION['error'] = "No task selected for deletion.";
    header("Location: ../../student/task.php");
    exit();
}

$taskId = $_POST['task_id'];
$studentId = $_SESSION['userId']; // Get the student ID from the session

// Delete the student's answer from the taskAnswer table
$deleteQuery = "DELETE FROM taskAnswer WHERE task_id = ? AND student_id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("ii", $taskId, $studentId);

// Execute the delete query and check if successful
if ($deleteStmt->execute()) {
    $_SESSION['success'] = "Answer deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete the answer. Please try again.";
}

// Close the statement and redirect back to the task page
$deleteStmt->close();
header("Location: ../../student/task.php");
exit();
?>