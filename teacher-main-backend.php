<?php
// Include database connection details
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = $conn->real_escape_string(trim($_POST['title']));
    $instruction = $conn->real_escape_string(trim($_POST['instruction']));
    $date = $conn->real_escape_string(trim($_POST['date']));
    $duration = intval(trim($_POST['duration'])); // Duration in days

    // Validate the data
    if (empty($title) || empty($instruction) || empty($date) || empty($duration)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: create-assignment.php');
        exit();
    }

    // Prepare and execute the insert statement
    $sql = "INSERT INTO assignments (title, instruction, date, duration, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $title, $instruction, $date, $duration);

    if ($stmt->execute()) {
        // Success
        header('Location: teacher-main.php?status=success');
    } else {
        // Error
        header('Location: teacher-main.php?status=error');
    }

    $stmt->close();
}

$conn->close();
?>
