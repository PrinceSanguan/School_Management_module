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

// Fetch the task ID and student ID from the POST request
$taskId = $_POST['task_id'];
$studentId = $_POST['student_id'];

// Delete the student's answer from the taskAnswer table
$deleteQuery = "DELETE FROM taskAnswer WHERE task_id = ? AND student_id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("ii", $taskId, $studentId);

if ($deleteStmt->execute()) {
    $_SESSION['success'] = "Answer deleted successfully.";
    header("Location: ../../student/task.php");
} else {
    $_SESSION['error'] = "Failed to delete the answer. Please try again.";
    header("Location: ../../student/task.php");
}

// Redirect back to the view page
header("Location: ../../student/task.php");
exit();
?>