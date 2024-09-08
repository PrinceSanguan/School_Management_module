<?php
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is set
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get the ID from the query string

    // Prepare and execute delete statement
    $sql = "DELETE FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        // Error preparing the statement
        header("Location: teachers-admin.php?status=error");
        exit();
    }
    
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect with success message
        header("Location: teachers-admin.php?status=deleted");
    } else {
        // Redirect with error message
        header("Location: teachers-admin.php?status=error");
    }

    $stmt->close();
} else {
    // Redirect if ID is not set
    header("Location: teachers-admin.php?status=error");
}

$conn->close();

