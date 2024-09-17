<?php
include "../../database/database.php";
require "../../database/config.php";

$date = $_POST['date'];
$title = $_POST['title'];

$sql = "INSERT INTO events (date, title) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $date, $title);

if ($stmt->execute()) {
    echo "Event added successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
