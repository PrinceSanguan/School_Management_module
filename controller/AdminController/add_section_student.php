<?php
include "../../database/database.php";
require "../../database/config.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $_POST['student'];
    $section_id = $_POST['section'];

    try {
        // Insert the student and section into studentSection
        $insertQuery = "INSERT INTO studentSection (user_id, section_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->execute([$student_id, $section_id]);

        $_SESSION['success'] = 'Student has been successfully assigned to the section!';
        header("Location: ../../admin/student-registration.php");

    } catch (PDOException $e) {
      $_SESSION['error'] = 'The student didnt Register';
      header("Location: ../../admin/student-registration.php");
    }
}
?>
