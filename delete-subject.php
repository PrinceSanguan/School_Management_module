<?php
include 'db_connect.php'; 

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'An unexpected error occurred.'];

if ($conn->connect_error) {
    $response['message'] = 'Connection failed: ' . $conn->connect_error;
    echo json_encode($response);
    exit;
}

$subject_id = intval($_GET['id']);

// Prepare and execute the deletion
$stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);

if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'Subject deleted successfully.';
} else {
    $response['message'] = 'Error deleting subject: ' . $stmt->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>
