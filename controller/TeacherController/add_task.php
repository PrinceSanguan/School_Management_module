<?php
include "../../database/database.php";
require "../../database/config.php";


// Get form data
$subject_id = $_POST['subject'] ?? '';
$task_title = $_POST['task_title'] ?? '';
$content = $_POST['content'] ?? '';
$date = $_POST['deadline'] ?? '';
$postType = $_POST['postType'] ?? ''; // Determine whether it's an image or text post

// Check if an image file was uploaded
$image = $_FILES['image'] ?? null;

// Validate required fields
if (empty($postType)) {
  $_SESSION['error'] = 'Please select post type (Image or Text).';
  header("Location: ../../teacher/task.php");
  exit();
}

// Validate required subject
if (empty($subject_id)) {
  $_SESSION['error'] = 'Please select a subject.';
  header("Location: ../../teacher/task.php");
  exit();
}

// Validate deadline
if (empty($date)) {
  $_SESSION['error'] = 'Please choose a deadline.';
  header("Location: ../../teacher/task.php");
  exit();
}

// Initialize an array to hold image paths if multiple files are uploaded
$imagePaths = [];

// Check if post type is 'Image' and files are uploaded
if ($postType == 'Image') {
    if (!empty($image['name'][0])) {
        // Loop through each file
        foreach ($image['name'] as $key => $fileName) {
            // Validate the file type
            $fileType = $image['type'][$key];
            $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = 'Only JPG, PNG, and PDF files are allowed.';
                header("Location: ../../teacher/task.php");
                exit();
            }

            // Create the upload directory if it doesn't exist
            $uploadDir = '../../uploads/task/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate a unique file name and move the file to the target directory
            $targetFilePath = $uploadDir . time() . '_' . basename($fileName);
            if (move_uploaded_file($image['tmp_name'][$key], $targetFilePath)) {
                // Store the relative path (for example, from the web root)
                $imagePaths[] = str_replace('../../', '', $targetFilePath);
            } else {
                $_SESSION['error'] = 'Failed to upload the image: ' . $fileName;
                header("Location: ../../teacher/task.php");
                exit();
            }
        }

        // Convert the array of image paths to a string (you can also save them as JSON or a separate table if needed)
        $imagePath = implode(',', $imagePaths);  // Save image paths as a comma-separated string
        $content = null;  // Since this is an image post, set content to null
    } else {
        $_SESSION['error'] = 'Please upload at least one image.';
        header("Location: ../../teacher/task.php");
        exit();
    }
} elseif ($postType == 'text') {
    // Handle the text case here
    if (empty($content)) {
        $_SESSION['error'] = 'Please provide the task content.';
        header("Location: ../../teacher/task.php");
        exit();
    }
    $imagePath = null;  // Set imagePath to null as this is a text post
}

// Insert the task into the database
$sql = "INSERT INTO task (subject_id, task_title, content, image_path, deadline, status) 
        VALUES (?, ?, ?, ?, ?, 'active')";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param('issss', $subject_id, $task_title, $content, $imagePath, $date);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Task added successfully!';
        header("Location: ../../teacher/task.php");
        exit();
    } else {
        $_SESSION['error'] = 'Error adding task: ' . $stmt->error;
        header("Location: ../../teacher/task.php");
        exit();
    }
} else {
    $_SESSION['error'] = 'Error preparing the SQL statement: ' . $conn->error;
    header("Location: ../../teacher/task.php");
    exit();
}
?>
