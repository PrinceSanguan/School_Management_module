<?php
include "../../database/database.php"; // Adjust the path if necessary

if (isset($_GET['subject_id'])) {
    $subject_id = intval($_GET['subject_id']);
    
    // Prepare response array
    $response = [
        'week1' => [],
        'week2' => [],
        'week3' => [],
        'week4' => []
    ];

    // Fetch data from the database
    $stmt = $conn->prepare("SELECT week, image_url FROM subject_images WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if (isset($response[$row['week']])) {
            $response[$row['week']][] = $row['image_url'];
        }
    }

    $stmt->close();
    $conn->close();

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Subject ID not provided']);
    exit();
}