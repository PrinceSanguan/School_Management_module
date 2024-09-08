<?php
// Include database connection details
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate the cutoff date for deletion
$sql = "DELETE FROM assignments WHERE DATE_ADD(created_at, INTERVAL duration DAY) < NOW()";
if ($conn->query($sql) === TRUE) {
    echo "Expired assignments deleted successfully.";
} else {
    echo "Error deleting records: " . $conn->error;
}

$conn->close();
?>
