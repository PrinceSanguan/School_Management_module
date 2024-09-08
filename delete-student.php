<?php
include 'db_connect.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        header("Location: student-admin.php?status=error");
        exit();
    }
    
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: student-admin.php?status=deleted");
    } else {
        header("Location: student-admin.php?status=error");
    }

    $stmt->close();
} else {
    header("Location: student-admin.php?status=error");
}

$conn->close();

