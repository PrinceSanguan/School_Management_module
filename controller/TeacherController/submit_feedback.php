<?php
include "../../database/database.php"; // Adjust the path if necessary

session_start();

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $taskId = isset($_POST['task_id']) ? intval($_POST['task_id']) : null;
    $studentId = isset($_POST['student_id']) ? intval($_POST['student_id']) : null;
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : null;

    // Check if task_id and student_id are valid
    if ($taskId && $studentId && !empty($feedback)) {
        // Prepare the SQL statement to update the feedback
        $updateFeedbackQuery = "UPDATE taskAnswer 
                                SET feedback = ? 
                                WHERE task_id = ? 
                                AND student_id = ?";

        $updateStmt = $conn->prepare($updateFeedbackQuery);
        $updateStmt->bind_param('sii', $feedback, $taskId, $studentId);

        // Try to execute the statement
        if ($updateStmt->execute()) {
            $_SESSION['success'] = "Feedback submitted successfully!";
        } else {
            $_SESSION['error'] = "Failed to submit feedback. Please try again.";
        }

        // Close the prepared statement
        $updateStmt->close();
    } else {
        $_SESSION['error'] = "Invalid input. Please ensure feedback is provided.";
    }

    // Redirect back to the task answers page
    header("Location: ../../teacher/task.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../teacher/task_list.php");
    exit();
}

// Close the database connection
$conn->close();
?>