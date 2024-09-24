<?php
// Include the database connection file
include "../../database/database.php"; 

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Check if the user is a student
if ($_SESSION['userRole'] !== 'student') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

// Fetch the student ID from the session
$userId = $_SESSION['userId'];


// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Get the form data
    $task_id = $_POST['task_id']; // Hidden input for task ID
    $student_id = $userId; // Assuming student_id is stored in session when student logs in
    $postType = $_POST['postType']; // Dropdown to choose between image or text
    $text_answer = null; // Initialize as null
    $image_path = null;  // Initialize as null
    
    // Handle image upload if the user selects "Image"
    if ($postType == 'Image' && isset($_FILES['image_path']) && $_FILES['image_path']['error'] == 0) {
        // Define the upload directory
        $target_dir = "../../uploads/answer/"; // Make sure this directory exists and is writable
        $image_path = $target_dir . basename($_FILES["image_path"]["name"]);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $image_path)) {
            $image_path = basename($_FILES["image_path"]["name"]); // Store the filename in the DB
        } else {
            $_SESSION['error'] = "There was an error uploading the image.";
            header("Location: ../../student/task.php"); // Redirect back to the form
            exit();
        }
    }
    
    // Handle text submission if the user selects "Text"
    if ($postType == 'Text') {
        $text_answer = $_POST['text_answer'];
    }

    // Insert data into the database (only feedback is set to NULL by default)
    $sql = "INSERT INTO taskAnswer (task_id, student_id, text_answer, image_path, feedback) VALUES (?, ?, ?, ?, NULL)";
    
    // Prepare and execute the SQL statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $task_id, $student_id, $text_answer, $image_path);

    if ($stmt->execute()) {
        // If successful, set a success session message
        $_SESSION['success'] = 'your answer is been submitted!';
        header("Location: ../../student/task.php");
    } else {
        // If an error occurs, set an error session message
        $_SESSION['error'] = "Failed to submit the answer.";
        header("Location: ../../student/task.php");
    }

    // Redirect to the view page
    header("Location: ../../student/task.php");
    exit();
}
?>