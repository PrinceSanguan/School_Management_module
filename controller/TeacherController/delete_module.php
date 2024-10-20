<?php
include "../../database/database.php";
session_start();

// Check if the user is a teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    $_SESSION['error'] = "You do not have permission to perform this action!";
    header("Location: ../../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $image_url = $_POST['image_url'];
    $subject_id = $_POST['subject_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete the record from the database
        $query = "DELETE FROM subject_images WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("No record found to delete.");
        }

        // Delete the file from the uploads folder
        $file_path = "../uploads/" . $image_url;
        if (file_exists($file_path)) {
            if (!unlink($file_path)) {
                throw new Exception("Failed to delete the file.");
            }
        }

        // If everything is successful, commit the transaction
        $conn->commit();

        $_SESSION['success'] = "Module deleted successfully.";
    } catch (Exception $e) {
        // If there's an error, rollback the changes
        $conn->rollback();
        $_SESSION['error'] = "Error deleting module: " . $e->getMessage();
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the modules page
    header("Location: ../../teacher/subject_modules.php?subject_id=" . $subject_id);
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../../index.php");
    exit();
}
?>