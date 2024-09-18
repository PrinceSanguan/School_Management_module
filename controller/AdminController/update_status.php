<?php
include "../../database/database.php";
require "../../database/config.php";

session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectId = $_POST['subject_id'];
    $newStatus = $_POST['status'];

    // Update the status in the database
    $sql = "UPDATE teacherSubject SET status = ? WHERE subject_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $subjectId);
    
    if ($stmt->execute()) {
        // Respond with success message
        echo json_encode(['status' => 'success', 'message' => 'Status has been updated']);
    } else {
        // Respond with error message
        echo json_encode(['status' => 'error', 'message' => 'Status has not been updated']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Optionally, log errors instead of displaying them
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Disable error display
ini_set('log_errors', '1'); // Enable error logging
ini_set('error_log', 'path/to/your/error.log'); // Set the error log file path
?>