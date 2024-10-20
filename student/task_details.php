<?php
include "../database/database.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Check if the user is a student
if ($_SESSION['userRole'] !== 'student') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

// Fetch the subject ID from the URL
if (!isset($_GET['subject_id'])) {
    $_SESSION['error'] = "No subject selected.";
    header("Location: task.php");
    exit();
}

$subjectId = $_GET['subject_id'];
$userId = $_SESSION['userId'];

// Fetch subject details
$subjectQuery = "SELECT subject FROM subject WHERE id = ?";
$subjectStmt = $conn->prepare($subjectQuery);
$subjectStmt->bind_param("i", $subjectId);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();
$subject = $subjectResult->fetch_assoc();

if (!$subject) {
    $_SESSION['error'] = "Subject not found.";
    header("Location: task.php");
    exit();
}

// Fetch all active tasks for the subject
$taskQuery = "SELECT id, task_title, content, deadline, image_path 
              FROM task 
              WHERE subject_id = ? AND status = 'active'";
$taskStmt = $conn->prepare($taskQuery);
$taskStmt->bind_param("i", $subjectId);
$taskStmt->execute();
$taskResult = $taskStmt->get_result();

// Fetch answers for all tasks
$answerQuery = "SELECT task_id, text_answer, image_path, feedback 
                FROM taskAnswer 
                WHERE student_id = ?";
$answerStmt = $conn->prepare($answerQuery);
$answerStmt->bind_param("i", $userId);
$answerStmt->execute();
$answerResult = $answerStmt->get_result();

$answers = [];
while ($row = $answerResult->fetch_assoc()) {
    $answers[$row['task_id']] = $row;
}

// Close statements and connection
$subjectStmt->close();
$taskStmt->close();
$answerStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title><?= htmlspecialchars($subject['subject']) ?> - Tasks</title>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php">Modules</a>
    <a href="../student/task.php">Task</a>
    <a href="../student/profile.php">Profile</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
</div>

<div class="container">
    <h2>Tasks for <?= htmlspecialchars($subject['subject']) ?></h2>

    <?php 
while ($task = $taskResult->fetch_assoc()): 
    $isDeadlinePassed = strtotime($task['deadline']) < time();
    $hasAnswer = isset($answers[$task['id']]);
?>
    <div class="task-container">
        <h3><?= htmlspecialchars($task['task_title']) ?></h3>
        <table>
            <tr>
                <th>Task Details</th>
                <td>
                    <?php if (!empty($task['content'])): ?>
                        <?= htmlspecialchars($task['content']) ?>
                    <?php elseif (!empty($task['image_path'])): ?>
                        <img src="../<?= htmlspecialchars($task['image_path']) ?>" alt="Task Image" style="max-width: 200px;">
                    <?php else: ?>
                        <p>No content or image available.</p>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Deadline</th>
                <td>
                    <?= htmlspecialchars($task['deadline']) ?>
                    <?php if ($isDeadlinePassed): ?>
                        <span style="color: red; font-weight: bold;"> (Deadline passed)</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <h4>Your Answer</h4>
        <?php if ($hasAnswer): ?>
            <?php $answer = $answers[$task['id']]; ?>
            <table>
                <tr>
                    <th>Submitted Answer</th>
                    <td>
                        <?php if (!empty($answer['image_path'])): ?>
                            <img src="../../uploads/answer/<?= htmlspecialchars($answer['image_path']) ?>" alt="Your Answer" style="max-width: 200px;">
                        <?php elseif (!empty($answer['text_answer'])): ?>
                            <p><?= htmlspecialchars($answer['text_answer']) ?></p>
                        <?php else: ?>
                            <p>No answer submitted yet.</p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Feedback</th>
                    <td><?= !empty($answer['feedback']) ? htmlspecialchars($answer['feedback']) : 'No feedback yet.' ?></td>
                </tr>
            </table>

            <?php if (!$isDeadlinePassed): ?>
                <!-- Delete Answer Functionality -->
                <form action="../controller/StudentController/delete_answer.php" method="post">
                    <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['id']) ?>">
                    <button type="submit" class="button delete-btn" style="background-color: red;">Delete Answer</button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <p>No answer submitted yet.</p>
        <?php endif; ?>

        <?php if (!$isDeadlinePassed && !$hasAnswer): ?>
            <!-- Submit New Answer Button -->
            <button class="button openModal" data-task-id="<?= htmlspecialchars($task['id']) ?>">Submit Answer</button>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</div>

<!---------------ADD MODAL---------------------------->
<div class="modal" id="accountModal">
    <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Submit Answer</h2>

        <!-- Form to add an announcement -->
        <form id="addAccountForm" method="post" action="../controller/StudentController/submit_taskAnswer.php" enctype="multipart/form-data">

            <!-- Hidden input for task ID -->
            <input type="hidden" name="task_id" id="taskId">

            <!-- Select option to choose what to post -->
            <label for="postType">What do you want to submit?</label>
            <select name="postType" id="postType">
                <option value="" disabled selected>Select an option</option>
                <option value="Image">Image</option>
                <option value="Text">Text</option>
            </select>

            <!-- Hidden image input field -->
            <div id="imageUpload" style="display: none;">
                <label>Upload Image or PDF</label>
                <input type="file" name="image_path" id="imageFile" accept=".png, .jpg, .jpeg, .pdf">
            </div>

            <!-- Hidden textarea input field -->
            <div id="textInput" style="display: none;">
                <label>Text answer</label>
                <textarea name="text_answer" rows="5" cols="65" placeholder="Submit your answer here"></textarea>
            </div>

            <!-- Submit button -->
            <button type="submit">Submit Answer</button>
        </form>
    </div>
</div>
<!---------------ADD MODAL---------------------------->

<!-- JavaScript to handle showing/hiding fields -->
<script>
  document.getElementById('postType').addEventListener('change', function() {
    var postType = this.value;
    var imageUpload = document.getElementById('imageUpload');
    var textInput = document.getElementById('textInput');

    // Hide both initially
    imageUpload.style.display = 'none';
    textInput.style.display = 'none';

    // Show image upload if "Image" is selected
    if (postType === 'Image') {
      imageUpload.style.display = 'block';
    }
    // Show textarea if "Text" is selected
    else if (postType === 'Text') {
      textInput.style.display = 'block';
    }
  });

  // Handle modal display
  document.querySelectorAll('.openModal').forEach(button => {
    button.addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'flex';

      // Set the task ID in the hidden input
      const taskId = this.getAttribute('data-task-id');
      document.getElementById('taskId').value = taskId;
    });

    document.getElementById('closeModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'none';
    });
  });
</script>

</body>
</html>




