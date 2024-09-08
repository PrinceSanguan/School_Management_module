<?php
session_start(); // Start the session

// Include database connection
include 'db_connect.php';

// Function to check if a teacher announcement exists
function checkAnnouncementExists($conn) {
    $sql = "SELECT COUNT(*) FROM teacher_announcement";
    $result = $conn->query($sql);
    $count = $result->fetch_row()[0];
    return $count > 0;
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Extract and sanitize form data
    $title = $_POST['title'];
    $a1 = $_POST['a1'];
    $a2 = $_POST['a2'];
    $a3 = $_POST['a3'];

    // Check if an announcement already exists
    if (checkAnnouncementExists($conn)) {
        // Redirect with an error message
        header('Location: teacher-announcement.php?status=exists');
        exit;
    }

    // Prepare and execute SQL query to insert announcement
    $sql = "INSERT INTO teacher_announcement (title, a1, a2, a3) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $title, $a1, $a2, $a3);

    if ($stmt->execute()) {
        // Redirect with a success message
        header('Location: teacher-announcement.php?status=success');
        exit;
    } else {
        // Redirect with an error message
        header('Location: teacher-announcement.php?status=error');
        exit;
    }
}
?>
