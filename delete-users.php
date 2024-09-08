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

    // Start transaction
    $conn->begin_transaction();

    try {
        // Delete related records from subjects table
        $sql = "DELETE FROM subjects WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing subjects delete statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Delete related records from submissions table
        $sql = "DELETE FROM submissions WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing submissions delete statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Check if the user exists before attempting to delete
        $sql = "SELECT COUNT(*) FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing user check statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            throw new Exception("User with ID $id does not exist.");
        }

        // Delete the user
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing user delete statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();
        // Redirect with success message
        header("Location: users-admin.php?status=deleted");
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        // Log error for debugging
        error_log($e->getMessage());
        // Redirect with error message
        header("Location: users-admin.php?status=error");
    }

} else {
    // Redirect if ID is not set
    header("Location: users-admin.php?status=error");
}

$conn->close();
?>
