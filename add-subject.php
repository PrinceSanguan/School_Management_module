<?php
include 'db_connect.php'; 

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'An unexpected error occurred.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $subjects = $_POST['subjects'];

    if (empty($user_id) || empty($subjects)) {
        $response['message'] = 'User and subjects cannot be empty.';
        echo json_encode($response);
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("User not found.");
        }

        // Insert subjects
        $stmt = $conn->prepare("INSERT INTO subjects (user_id, subject_name) VALUES (?, ?)");
        foreach ($subjects as $subject) {
            if (!empty(trim($subject))) {
                $stmt->bind_param("is", $user_id, $subject);
                $stmt->execute();
            }
        }

        // Commit transaction
        $conn->commit();

        $response['status'] = 'success';
        $response['message'] = 'Subjects added successfully.';
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        $response['message'] = $e->getMessage();
    }

    $stmt->close();
    $conn->close();
}

echo json_encode($response);
?>
