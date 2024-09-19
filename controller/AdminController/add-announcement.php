<?php
include "../../database/database.php";
require "../../database/config.php";

// Start session to store success or error messages
session_start();

// Get form data
$announcement = $_POST['announcement'] ?? '';
$view = $_POST['view'] ?? '';

// Check if an image file was uploaded
$image = $_FILES['image'] ?? null;

// Validate required fields
if (empty($announcement) && empty($image['name'])) {
    $_SESSION['error'] = 'Please enter an announcement or upload an image.';
    header("Location: ../../admin/announcement.php");
    exit();
}

if (empty($view)) {
    $_SESSION['error'] = 'Please select who can view this announcement.';
    header("Location: ../../admin/announcement.php");
    exit();
}

// Initialize variables for the image path
$imagePath = null;

if (!empty($image) && $image['name'] !== '') {
    // Validate the image (optional: size, type)
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($image['type'], $allowedTypes)) {
        $_SESSION['error'] = 'Only JPG, PNG, and PDF files are allowed.';
        header("Location: ../../admin/announcement.php");
        exit();
    }

    // Create the folder if it doesn't exist
    $uploadDir = '../../uploads/announcements/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Create a unique name for the uploaded image
    $imagePath = $uploadDir . time() . '_' . basename($image['name']);

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
        $_SESSION['error'] = 'Failed to upload the image. Please try again.';
        header("Location: ../../admin/announcement.php");
        exit();
    }

    // Store the relative path (for example, from the web root)
    $imagePath = str_replace('../../', '', $imagePath);
}

// Insert announcement, image path, and automatically store created_at in the database
$query = "INSERT INTO announcement (announcement, view, image_path) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    $_SESSION['error'] = 'Failed to prepare statement: ' . $conn->error;
    $conn->close();
    header("Location: ../../admin/announcement.php");
    exit();
}

// Bind parameters
$stmt->bind_param("sss", $announcement, $view, $imagePath);

// Execute and check for errors
if (!$stmt->execute()) {
    $_SESSION['error'] = 'Failed to execute statement: ' . $stmt->error;
} else {
    $_SESSION['success'] = 'Your announcement has been published!';
}

// Close statement and connection
$stmt->close();
$conn->close();

// Redirect back to the announcement page
header("Location: ../../admin/announcement.php");
exit();
?>
