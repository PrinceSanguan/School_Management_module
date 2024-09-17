<?php
include "../../database/database.php";
require "../../database/config.php";

// Check if the ID is set
if (!isset($_POST['id'])) {
    die("Error: No event ID provided");
}

$id = $_POST['id'];

// Validate that $id is a number
if (!is_numeric($id)) {
    die("Error: Invalid event ID");
}

$sql = "DELETE FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Event deleted successfully. Rows affected: " . $stmt->affected_rows;
} else {
    echo "Error executing statement: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>