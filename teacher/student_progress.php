<?php
include "../database/database.php";
session_start();

// Check if the user is a teacher
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'teacher') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'You do not have permission to access this data.']);
    exit();
}

if (!isset($_GET['subject_id']) || !isset($_GET['section'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'No subject or section selected.']);
    exit();
}

$subjectId = filter_var($_GET['subject_id'], FILTER_VALIDATE_INT);
$section = filter_var($_GET['section'], FILTER_SANITIZE_STRING);

try {
    // Fetch the total number of images across ALL subjects
    $imageQuery = "SELECT COUNT(image_url) AS numTotalImages FROM subject_images";
    $imageResult = $conn->query($imageQuery);
    $numTotalImages = $imageResult->fetch_assoc()['numTotalImages'];

    // Fetch students enrolled in the subject with their progress
    $query = "SELECT 
                u.id, 
                u.firstName, 
                u.lastName, 
                u.progress AS numUserProgress
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
        echo "<table class='progress-table'>
                <tr>
                    <th>Student Name</th>
                    <th>Progress</th>
                    <th>Details</th>
                </tr>";
        
        while ($row = $result->fetch_assoc()) {
            // Calculate progress percentage using the same logic as student dashboard
            $numUserProgress = $row['numUserProgress'] ?? 0;
            $progressPercentage = ($numTotalImages > 0) ? ($numUserProgress / $numTotalImages) * 100 : 0;
            $progressPercentage = min(100, max(0, $progressPercentage)); // Ensure between 0-100

            // Get appropriate message based on progress
            $messageIndex = floor($progressPercentage / 10);
            $messages = [
                '0% - Just getting started',
                '10% - Making progress',
                '20% - Great Job',
                '30% - Doing amazing',
                '40% - Almost halfway',
                '50% - Halfway there',
                '60% - In the home stretch',
                '70% - Almost there',
                '80% - So close',
                '90% - Nearly complete',
                '100% - Completed'
            ];
            $message = $messages[min(10, $messageIndex)];

            echo "<tr>
                    <td>" . htmlspecialchars($row['lastName'] . ", " . $row['firstName']) . "</td>
                    <td>
                        <div class='progress-bar-container'>
                            <div class='progress-bar' 
                                 style='width: " . htmlspecialchars($progressPercentage) . "%; 
                                        background-color: " . getProgressColor($progressPercentage) . ";'>
                                " . round($progressPercentage) . "%
                            </div>
                        </div>
                    </td>
                    <td>
                        " . htmlspecialchars($numUserProgress) . " of " . $numTotalImages . " Modules viewed
                        <br>
                        <small>" . htmlspecialchars($message) . "</small>
                    </td>
                  </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p class='no-results'>No students found enrolled in this subject for the selected section.</p>";
    }

} catch (Exception $e) {
    error_log("Error in progress calculation: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'An error occurred while processing your request.']);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}

function getProgressColor($percentage) {
    if ($percentage < 33) return '#ff6b6b';
    if ($percentage < 66) return '#ffd93d';
    return '#6bcb77';
}
?>

<style>
.progress-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.progress-table th,
.progress-table td {
    padding: 10px;
    border: 1px solid #ddd;
}

.progress-bar-container {
    width: 100%;
    background-color: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar {
    padding: 5px;
    color: white;
    text-align: center;
    transition: width 0.3s ease;
    min-width: 30px;
}

.no-results {
    color: #666;
    font-style: italic;
}
</style>