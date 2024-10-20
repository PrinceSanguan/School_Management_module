<?php
include "../../database/database.php"; // Adjust the path if necessary

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Check if subject_id is coming from POST data
    if (!isset($_POST['subject_id'])) {
        $_SESSION['error'] = "Subject ID not set in POST data.";
        header("Location: ../../teacher/assign_subject.php");
        exit();
    }

    // Get the common POST data
    $subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : null;
    $week = isset($_POST['week']) ? $_POST['week'] : null;
    $postType = isset($_POST['postType']) ? $_POST['postType'] : null;

    // Debugging: Ensure $subject_id has a value after assignment
    if (!$subject_id) {
        $_SESSION['error'] = "Subject ID is invalid or not provided.";
        header("Location: ../../teacher/assign_subject.php");
        exit();
    }

    // Set default values for image_url and youtube_url as spaces
    $image_url = " ";
    $youtube_url = " ";

    // Check if we are dealing with a PDF module or an embedded video
    if ($postType === 'Image' && isset($_FILES['pdfFiles'])) {
        // Handle PDF file uploads
        $uploadedFiles = $_FILES['pdfFiles'];
        $fileCount = count($uploadedFiles['name']);
        $uploadDirectory = "../../uploads/"; // Ensure this directory exists and is writable

        // Prepare an array to hold uploaded file paths
        $filePaths = [];

        for ($i = 0; $i < $fileCount; $i++) {
            $fileTmpName = $uploadedFiles['tmp_name'][$i];
            $fileName = basename($uploadedFiles['name'][$i]);
            $filePath = $uploadDirectory . $fileName;

            // Move the uploaded file to the upload directory
            if (move_uploaded_file($fileTmpName, $filePath)) {
                $filePaths[] = $filePath;
            } else {
                $_SESSION['error'] = "Failed to upload file: $fileName.";
                header("Location: ../../teacher/assign_subject.php"); // Redirect to the page with the form
                exit();
            }
        }

        // Use the first PDF file path (or adjust based on your requirements)
        $image_url = $filePaths[0];
    } elseif ($postType === 'Text' && isset($_POST['youtube_url'])) {
        // Handle embedded video
        $youtube_url = $_POST['youtube_url'];
    }

    // Set default values to spaces if empty
    if (empty($image_url)) {
        $image_url = " ";
    }
    if (empty($youtube_url)) {
        $youtube_url = " ";
    }

    // Insert data into the database
    if ($subject_id && $week) {
        $stmt = $conn->prepare("INSERT INTO subject_images (subject_id, week, image_url, youtube_url) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("isss", $subject_id, $week, $image_url, $youtube_url);
            $stmt->execute();
            $stmt->close();
            $_SESSION['success'] = "Module added successfully.";
        } else {
            $_SESSION['error'] = "Database error: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Invalid submission: missing subject ID or week.";
    }

    $conn->close();

    // Redirect with subject_id only if it's valid
    if ($subject_id) {
        header("Location: ../../teacher/subject_modules.php?subject_id=" . $subject_id); // Redirect to the page with the form
    } else {
        header("Location: ../../teacher/assign_subject.php"); // Redirect back to the form on error
    }
    exit();
} else {
    // Redirect if not a POST request
    header("Location: ../../teacher/subject_modules.php?subject_id=" . (isset($subject_id) ? $subject_id : ''));
    exit();
}