<?php
include 'db_connect.php'; 
session_start();

$title = $conn->real_escape_string(trim($_POST['title']));
$r1 = $conn->real_escape_string(trim($_POST['r1']));
$r2 = $conn->real_escape_string(trim($_POST['r2']));
$r3 = $conn->real_escape_string(trim($_POST['r3']));

// Validate the data
if (empty($title) || empty($r1) || empty($r2) || empty($r3)) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: recent-activities.php');
    exit();
}

// Check if the record already exists
$sql_check = "SELECT COUNT(*) FROM recent_activities WHERE title = ?";
$stmt_check = $conn->prepare($sql_check);
if ($stmt_check === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt_check->bind_param("s", $title);
$stmt_check->execute();
$stmt_check->bind_result($count);
$stmt_check->fetch();
$stmt_check->close();

if ($count > 0) {
    // Record already exists
    header('Location: recent-activities.php?status=exist');
    exit();
}

// Prepare and execute the insert statement
$sql_insert = "INSERT INTO recent_activities (title, r1, r2, r3) VALUES (?, ?, ?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
if ($stmt_insert === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt_insert->bind_param("ssss", $title, $r1, $r2, $r3);

if ($stmt_insert->execute()) {
    header('Location: recent-activities.php?status=success');
} else {
    header('Location: recent-activities.php?status=error');
}

$stmt_insert->close();
$conn->close();
?>
