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
    $teacher_id = isset($_POST['teacher']) ? $_POST['teacher'] : null;
    $subject_id = isset($_POST['subject']) ? $_POST['subject'] : null;

    // Validate inputs
    if (empty($teacher_id) || empty($subject_id)) {
        $_SESSION['error'] = "Please select both a teacher and a subject.";
        header("Location: ../../admin/registration.php"); // Redirect back to the form page
        exit();
    }

    // Check if the selected user is indeed a teacher
    $teacherCheckQuery = "SELECT userRole FROM users WHERE id = ? AND userRole = 'teacher'";
    $stmt = $conn->prepare($teacherCheckQuery);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $_SESSION['error'] = "The selected user is not a teacher.";
        header("Location: ../../admin/registration.php"); // Redirect back to the form page
        exit();
    }

    // Check if the teacher is already assigned to the subject
    $checkAssignmentQuery = "SELECT id FROM teacherSubject WHERE user_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($checkAssignmentQuery);
    $stmt->bind_param("ii", $teacher_id, $subject_id);
    $stmt->execute();
    $stmt->store_result();

    // Add condition to check if teacher is already assigned to the subject
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "The teacher already assigned that subject.";
        header("Location: ../../admin/registration.php"); // Redirect back to the form page
        exit();
    }

    // If the teacher is not assigned yet, insert the new teacher-subject assignment
    $insertQuery = "INSERT INTO teacherSubject (user_id, subject_id) VALUES (?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ii", $teacher_id, $subject_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Teacher successfully assigned to the subject!";
        header("Location: ../../admin/registration.php"); // Redirect back to the form page
    } else {
        $_SESSION['error'] = "An error occurred while assigning the teacher to the subject.";
        header("Location: ../../admin/registration.php"); // Redirect back to the form page
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../admin/registration.php");
    exit();
}