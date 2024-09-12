<?php
include "../../database/database.php";
require "../../database/config.php";
session_start(); // Ensure sessions are started

// Get ID from the query string
$id = $_GET['id'] ?? '';

// Validate ID
if (empty($id) || !is_numeric($id)) {
    $_SESSION['error'] = 'Invalid ID.';
    header("Location: ../../admin/announcement.php");
    exit();
}

// Start a transaction
$conn->begin_transaction();

try {
    // Prepare and execute the delete query
    $deleteQuery = "DELETE FROM announcement WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    $stmt->close();
    
    // Commit the transaction
    $conn->commit();
    
    // Close the connection
    $conn->close();
    
    // Redirect to the announcement page
    header("Location: ../../admin/announcement.php");
    exit();
} catch (Exception $e) {
    // Rollback the transaction if there is an error
    $conn->rollback();
    
    // Set the error message and redirect
    $_SESSION['error'] = $e->getMessage();
    $conn->close();
    header("Location: ../../admin/announcement.php");
    exit();
}
?>