<?php
include "../../database/database.php";
require "../../database/config.php";

// SQL query to fetch events including id
$sql = "SELECT id, date, title FROM events";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Initialize an array to store the events
    $events = array();
    
    // Fetch each row and add it to the events array
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    
    // Convert the events array to JSON for easy handling in the view
    echo json_encode($events);
} else {
    echo json_encode(array("message" => "No events found."));
}

$conn->close();
?>