<?php
include "../../database/database.php";
require "../../database/config.php";

// Get form data
$announcement = $_POST['announcement'] ?? '';
$view = $_POST['view'] ?? '';

// Validate required fields
if (empty($announcement) || empty($view)) {
    $_SESSION['error'] = 'All fields are required.';
    header("Location: ../../admin/announcement.php");
    exit();
}

$query = "INSERT INTO announcement (announcement, view) VALUES (?, ?)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    $_SESSION['error'] = 'Failed to prepare statement: ' . $conn->error;
    $conn->close();
    header("Location: ../../admin/announcement.php");
    exit();
}

$stmt->bind_param("ss", $announcement, $view);

if (!$stmt->execute()) {
    $_SESSION['error'] = 'Failed to execute statement: ' . $stmt->error;
}

$stmt->close();
$conn->close();

$_SESSION['success'] = 'Your announcement is already publish!';
header("Location: ../../admin/announcement.php");
exit();
?>