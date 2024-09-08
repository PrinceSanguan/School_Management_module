<?php
include 'db_connect.php'; 

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unexpected error occurred.'];

if ($conn->connect_error) {
    $response['message'] = 'Connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit;
}

$subject_id = intval($_POST['subject_id']);
$subject_name = $conn->real_escape_string($_POST['subject_name']);

// Prepare and execute the update
$stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE id = ?");
$stmt->bind_param("si", $subject_name, $subject_id);

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Subject updated successfully.';
} else {
    $response['message'] = 'Error updating subject: ' . $stmt->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>
