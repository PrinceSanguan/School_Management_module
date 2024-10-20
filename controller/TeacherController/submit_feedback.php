<?php
include "../../database/database.php"; // Adjust the path if necessary

session_start();

header('Content-Type: application/json');

// Function to handle the feedback submission
function submitFeedback($conn, $taskId, $studentId, $answerId, $feedback) {
    // Prepare the SQL statement to update the feedback
    $updateFeedbackQuery = "UPDATE taskAnswer 
                            SET feedback = ? 
                            WHERE id = ? 
                            AND task_id = ? 
                            AND student_id = ?";
    
    $stmt = $conn->prepare($updateFeedbackQuery);
    $stmt->bind_param('siii', $feedback, $answerId, $taskId, $studentId);

    // Try to execute the statement
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Feedback submitted successfully!"];
    } else {
        return ["success" => false, "message" => "Failed to submit feedback: " . $conn->error];
    }
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $taskId = isset($_POST['task_id']) ? intval($_POST['task_id']) : null;
    $studentId = isset($_POST['student_id']) ? intval($_POST['student_id']) : null;
    $answerId = isset($_POST['answer_id']) ? intval($_POST['answer_id']) : null;
    $feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : null;

    // Check if all required fields are provided
    if ($taskId && $studentId && $answerId && !empty($feedback)) {
        $result = submitFeedback($conn, $taskId, $studentId, $answerId, $feedback);
        echo json_encode($result);
    } else {
        echo json_encode(["success" => false, "message" => "Missing required fields."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

// Close the database connection
$conn->close();
?>