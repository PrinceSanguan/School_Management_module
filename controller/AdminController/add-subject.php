<?php
include "../../database/database.php";
require "../../database/config.php";
session_start(); // Ensure sessions are started

// Get form data
$section_id = $_POST['section_id'] ?? '';
$subject = $_POST['subject'] ?? '';

// Validate required fields
if (empty($section_id) || empty($subject)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: /school-management/admin/section.php");
    exit();
}

// Prepare and execute the insert query
$query = "INSERT INTO subject (section_id, subject) VALUES (?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['error'] = 'Failed to prepare statement: ' . $conn->error;
    $conn->close();
    header("Location: /school-management/admin/section.php");
    exit();
}

// Bind parameters and execute the statement
$stmt->bind_param("is", $section_id, $subject); // Correct parameter binding
if ($stmt->execute()) {
    $_SESSION['success'] = 'Subject added successfully!';
} else {
    $_SESSION['error'] = 'Failed to add subject: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect to the section page or any other page
header("Location: /school-management/admin/section.php");
exit();
?>