<?php
include 'db_connect.php'; 
session_start();

// Retrieve form data
$activity_id = $conn->real_escape_string(trim($_POST['id']));

// Validate the data
if (empty($activity_id)) {
    $_SESSION['error'] = 'No activity ID specified.';
    header('Location: recent-activities.php');
    exit();
}

// Prepare and execute the delete statement
$sql_delete = "DELETE FROM recent_activities WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
if ($stmt_delete === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt_delete->bind_param("i", $activity_id);

if ($stmt_delete->execute()) {
    header('Location: recent-activities.php?status=deleted');
} else {
    header('Location: recent-activities.php?status=error');
}

$stmt_delete->close();
$conn->close();
?>
