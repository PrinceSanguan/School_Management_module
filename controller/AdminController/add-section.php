<?php
include "../../database/database.php";
require "../../database/config.php";
session_start(); // Ensure sessions are started

// Get form data
$section = $_POST['section'] ?? '';

// Validate required fields
if (empty($section)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: /school-management/admin/section.php");
    exit();
}

// Prepare and execute the insert query
$query = "INSERT INTO section (section) VALUES (?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['error'] = 'Failed to prepare statement: ' . $conn->error;
    $conn->close();
    header("Location: /school-management/admin/section.php");
    exit();
}

// Bind parameters and execute the statement
$stmt->bind_param("s", $section); // Correct parameter binding
if ($stmt->execute()) {
    $_SESSION['success'] = 'Section added successfully!';
} else {
    $_SESSION['error'] = 'Failed to add section: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect to the section page
header("Location: /school-management/admin/section.php");
exit();
?>