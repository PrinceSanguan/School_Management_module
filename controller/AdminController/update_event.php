<?php
include "../../database/database.php";
require "../../database/config.php";

$id = $_POST['id'];
$title = $_POST['title'];

$sql = "UPDATE events SET title = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $title, $id);

if ($stmt->execute()) {
    echo "Event updated successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>