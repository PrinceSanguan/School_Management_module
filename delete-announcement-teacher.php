<?php
include 'db_connect.php'; // Include your database connection file

// Check if the ID is set in the POST request
if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Convert the ID to an integer for security

    // Check if ID is valid
    if ($id > 0) {
        // Prepare and execute the SQL DELETE query
        $sql = "DELETE FROM teacher_announcement WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header('Location: teacher-announcement.php?status=deleted');
            exit();
        } else {
            echo "Error deleting record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid ID";
    }
} else {
    echo "ID not set";
}

$conn->close();
?>
