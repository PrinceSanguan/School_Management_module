<?php
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch the image path from the database
    $sql = "SELECT image FROM modules WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        header("Location: teacher-module.php?status=error");
        exit();
    }
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // Delete the file from the modules directory
    if (!empty($image)) {
        $file_path = 'modules/' . $image;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Delete the record from the database
    $sql = "DELETE FROM modules WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        header("Location: teacher-module.php?status=error");
        exit();
    }
    
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: teacher-module.php?status=deleted");
    } else {
        header("Location: teacher-module.php?status=error");
    }

    $stmt->close();
} else {
    header("Location: teacher-module.php?status=error");
}

$conn->close();
?>
