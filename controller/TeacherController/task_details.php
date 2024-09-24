<?php
include "../../database/database.php"; // Adjust the path if necessary

session_start();

// Check if an ID was passed
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];  
} else {
    $_SESSION['error'] = "No task selected.";
    header("Location: ../teacher/task_list.php"); // Redirect if no task ID is provided
    exit();
}

// Fetch task title
$taskQuery = "SELECT task_title FROM task WHERE id = ?";
$taskStmt = $conn->prepare($taskQuery);
$taskStmt->bind_param("i", $taskId);
$taskStmt->execute();
$taskResult = $taskStmt->get_result();
$taskData = $taskResult->fetch_assoc();
$taskTitle = $taskData['task_title'];

// Fetch student answers for this task
$answerQuery = "SELECT ta.*, u.firstName, u.lastName 
                FROM taskAnswer ta
                JOIN users u ON ta.student_id = u.id
                WHERE ta.task_id = ?";
$answerStmt = $conn->prepare($answerQuery);
$answerStmt->bind_param("i", $taskId);
$answerStmt->execute();
$answerResult = $answerStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../asset/css/account-approval.css">
    <title>Student Answers for <?= htmlspecialchars($taskTitle) ?></title>
</head>
<body>

<div class="container">
    <h2>Task Title: <?= htmlspecialchars($taskTitle) ?></h2>
    
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student Answer</th>
                <th>Feedback</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($answerResult->num_rows > 0): ?>
                <?php while ($answer = $answerResult->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($answer['firstName'] . ' ' . $answer['lastName']) ?></td>
                        <td>
                            <!-- Display the student's answer (either text or image) -->
                            <?php if (!empty($answer['image_path'])): ?>
                                <img src="../../uploads/answer/<?= htmlspecialchars($answer['image_path']) ?>" alt="Submitted Image" style="max-width: 200px; height: auto;">
                            <?php elseif (!empty($answer['text_answer'])): ?>
                                <?= htmlspecialchars($answer['text_answer']) ?>
                            <?php else: ?>
                                No answer submitted.
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Display existing feedback -->
                            <?php if (!empty($answer['feedback'])): ?>
                                <?= htmlspecialchars($answer['feedback']) ?>
                            <?php else: ?>
                                No feedback yet.
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Form to provide feedback -->
                            <form method="post" action="submit_feedback.php">
                                <input type="hidden" name="task_id" value="<?= htmlspecialchars($taskId) ?>">
                                <input type="hidden" name="student_id" value="<?= htmlspecialchars($answer['student_id']) ?>">
                                
                                <label for="feedback_<?= htmlspecialchars($answer['id']) ?>">Feedback:</label>
                                <input type="text" name="feedback" id="feedback_<?= htmlspecialchars($answer['id']) ?>" value="<?= htmlspecialchars($answer['feedback']) ?>">
                                
                                <button type="submit" class="button">Submit Feedback</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No answers submitted for this task yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Close the statement and connection
$taskStmt->close();
$answerStmt->close();
$conn->close();
?>