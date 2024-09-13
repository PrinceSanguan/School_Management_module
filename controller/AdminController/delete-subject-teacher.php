<?php
// Include the database connection
include "../../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    $_SESSION['error'] = "You do not have permission to perform this action.";
    header("Location: ../../index.php");
    exit();
}

// Check if the form was submitted and validate the input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacher_id = isset($_POST['teacher_id']) ? $_POST['teacher_id'] : null;
    $subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;

    // Validate inputs
    if (empty($teacher_id) || empty($subject_id)) {
        $_SESSION['error'] = "Invalid data received.";
        header("Location: ../../admin/registration.php"); // Redirect back to the appropriate page
        exit();
    }

    // Prepare the delete query
    $deleteQuery = "DELETE FROM teacherSubject WHERE user_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($deleteQuery);

    if (!$stmt) {
        $_SESSION['error'] = 'Failed to prepare statement: ' . $conn->error;
        $conn->close();
        header("Location: ../../admin/registration.php"); // Redirect back to the appropriate page
        exit();
    }

    // Bind parameters and execute the delete query
    $stmt->bind_param("ii", $teacher_id, $subject_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Teacher-subject relationship deleted successfully!";
    } else {
        $_SESSION['error'] = "An error occurred while deleting the teacher-subject relationship.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect back to the appropriate page
    header("Location: ../../admin/registration.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../admin/registration.php");
    exit();
}
