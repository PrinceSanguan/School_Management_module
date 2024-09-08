<?php
// Include database connection details
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided and is a valid number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Prepare and execute the delete statement for submissions
        $sql = "DELETE FROM submissions WHERE assignment_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Prepare and execute the delete statement for the assignment
        $sql = "DELETE FROM assignments WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();
        header('Location: view-assignments.php?status=deleted');
    } catch (Exception $e) {
        // Rollback the transaction if there's an error
        $conn->rollback();
        header('Location: view-assignments.php?status=error');
    }
} else {
    // Redirect to view assignments with error status if ID is invalid
    header('Location: view-assignments.php?status=error');
}

$conn->close();
?>
