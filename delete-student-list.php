<?php
session_start();

include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve student ID from the query parameter
$student_id = intval($_GET['id']);

// Fetch the current image path
$sql = "SELECT image FROM students_grades WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Student not found.";
    header("Location: teacher-main.php");
    exit();
}

$student = $result->fetch_assoc();
$image_path = $student['image'];

// Delete student record
$sql = "DELETE FROM students_grades WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);

if ($stmt->execute()) {
    // If image exists, delete it from the server
    if (!empty($image_path) && file_exists($image_path)) {
        unlink($image_path);
    }

    $_SESSION['deleted'] = "Student deleted successfully.";
    header("Location: teacher-main.php");
    exit();
} else {
    $_SESSION['error'] = "Error: " . $stmt->error;
    header("Location: teacher-main.php");
    exit();
}

