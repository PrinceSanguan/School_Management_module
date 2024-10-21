<?php
include "../database/database.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $subjectId = $_POST['subject_id'];
    $week = $_POST['week'];

    // Check if the user has already viewed this module
    $checkQuery = "SELECT * FROM user_progress WHERE user_id = ? AND subject_id = ? AND week = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("iis", $userId, $subjectId, $week);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows == 0) {
        // If the user hasn't viewed this module before, insert a new record
        $insertQuery = "INSERT INTO user_progress (user_id, subject_id, week) VALUES (?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iis", $userId, $subjectId, $week);
        $insertStmt->execute();

        // Update the progress in the users table
        $updateQuery = "UPDATE users SET progress = COALESCE(progress, '0') + 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $userId);
        $updateStmt->execute();

        echo "Progress updated successfully";
    } else {
        echo "Module already viewed";
    }

    $checkStmt->close();
    $conn->close();
} else {
    echo "Invalid request";
}
?>