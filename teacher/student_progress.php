<?php
include "../database/database.php";
session_start();

// Check if the user is a teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    echo "You do not have permission to access this data.";
    exit();
}

if (!isset($_GET['subject_id']) || !isset($_GET['section'])) {
    echo "No subject or section selected.";
    exit();
}

$subjectId = intval($_GET['subject_id']);
$section = $_GET['section'];

// Fetch students enrolled in the subject
$query = "SELECT u.id, u.firstName, u.lastName
          FROM users u
          JOIN studentSection ss ON u.id = ss.user_id
          JOIN section sec ON ss.section_id = sec.id
          JOIN subject s ON sec.id = s.section_id
          WHERE s.id = ? AND sec.section = ? AND u.userRole = 'student'
          ORDER BY u.lastName, u.firstName";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $subjectId, $section);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h3>Enrolled Students in " . htmlspecialchars($section) . "</h3>";
    echo "<table border='1'>
            <tr>
                <th>Student Name</th>
            </tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['lastName'] . ", " . $row['firstName']) . "</td>
              </tr>";
    }
    
    echo "</table>";
} else {
    echo "No students found enrolled in this subject for the selected section.";
}

$stmt->close();
$conn->close();
?>