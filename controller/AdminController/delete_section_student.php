<?php
// Include the database connection
include "../../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    $_SESSION['error'] = "You do not have permission to perform this action.";
    header("Location: ../../index.php");
    exit();
}

// Handle delete action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_id'])) {
  $deleteId = $_POST['delete_id'];

  // Delete the entry from studentSection table
  $deleteQuery = "DELETE FROM studentSection WHERE id = ?";
  $stmt = $conn->prepare($deleteQuery);
  $stmt->execute([$deleteId]);

  // Refresh the page to reflect the deletion
  $_SESSION['success'] = 'The Student Section is deleted';
  header("Location: ../../admin/student-registration.php");
  exit();
}
