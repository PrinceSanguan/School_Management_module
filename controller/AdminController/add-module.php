<?php
include "../../database/database.php"; // Adjust the path if necessary

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['subject_id']) && isset($_POST['week']) && isset($_FILES['pdfFiles'])) {
        $subject_id = intval($_POST['subject_id']);
        $week = $_POST['week'];

        // Ensure subject_id is valid
        if (empty($subject_id) || empty($week) || !isset($_FILES['pdfFiles'])) {
            $_SESSION['error'] = "Invalid data.";
            header("Location: ../../controller/AdminController/view-subject.php"); // Redirect to the page with the form
            exit();
        }

        // Handle file uploads
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
                header("Location: ../../controller/AdminController/view-subject.php"); // Redirect to the page with the form
                exit();
            }
        }

        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO subject_images (subject_id, week, image_url) VALUES (?, ?, ?)");
        if ($stmt) {
            foreach ($filePaths as $filePath) {
                $stmt->bind_param("iss", $subject_id, $week, $filePath);
                $stmt->execute();
            }
            $stmt->close();
            $_SESSION['success'] = "Modules added successfully.";
        } else {
            $_SESSION['error'] = "Database error.";
        }

        $conn->close();
    } else {
        $_SESSION['error'] = "Invalid submission.";
    }

    header("Location: ../../admin/section.php"); // Redirect to the page with the form
    exit();
} else {
    // Redirect if not POST request
    header("Location: ../../controller/AdminController/view-subject.php");
    exit();
}
