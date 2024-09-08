<?php
// Include database connection
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $contact = filter_var($_POST['contact']);
    $address = filter_var($_POST['address']);

    if (!empty($password)) {
        // Update with password
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admins SET email = ?, password = ?, contact = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $email, $password, $contact, $address, $id);
    } else {
        // Update without password
        $sql = "UPDATE admins SET email = ?, contact = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $email, $contact, $address, $id);
    }

    if ($stmt->execute()) {
        // Redirect to the edit page with a success message
        header('Location: users-admin.php?status=success');
    } else {
        // Redirect to the edit page with an error message
        header('Location: update-admin.php?status=error');
    }

    $stmt->close();
}

// Close the database connection
$conn->close();

