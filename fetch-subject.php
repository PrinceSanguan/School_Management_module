<?php
require_once 'db_connect.php';

// Check if user is authenticated
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Create a new database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch subject details for the authenticated user
    $sql = "SELECT subject_name FROM subjects WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    // Check if prepare failed
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<ul class="list-unstyled">';
        while ($row = $result->fetch_assoc()) {
            echo '<li><strong>' . htmlspecialchars($row['subject_name']) . '</strong></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No subjects found.</p>';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo '<p>You must be logged in to view subject details.</p>';
}
?>
