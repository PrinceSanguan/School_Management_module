<?php
include "../../database/database.php";
require "../../database/config.php";
session_start(); // Ensure sessions are started

// Get section ID from the query string
$section_id = $_GET['section_id'] ?? '';

// Validate section ID
if (empty($section_id) || !is_numeric($section_id)) {
    $_SESSION['error'] = 'Invalid section ID.';
    header("Location: /school-management/admin/section.php");
    exit();
}

// Start a transaction
$conn->begin_transaction();

try {
    // Prepare and execute the delete query for subjects
    $deleteSubjectsQuery = "DELETE FROM subject WHERE section_id = ?";
    $stmt = $conn->prepare($deleteSubjectsQuery);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    $stmt->bind_param("i", $section_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete subjects: ' . $stmt->error);
    }
    $stmt->close();

    // Prepare and execute the delete query for the section
    $deleteSectionQuery = "DELETE FROM section WHERE id = ?";
    $stmt = $conn->prepare($deleteSectionQuery);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    $stmt->bind_param("i", $section_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete section: ' . $stmt->error);
    }
    $stmt->close();

    // Commit the transaction
    $conn->commit();
    $_SESSION['success'] = 'Section and related subjects deleted successfully!';
} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
}

// Close the connection
$conn->close();

// Redirect to the section page
header("Location: /school-management/admin/section.php");
exit();
?>