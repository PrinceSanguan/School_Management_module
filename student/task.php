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

// Fetch the student ID from the session
$userId = $_SESSION['userId'];

// Step 1: Fetch the student's section
$sectionQuery = "SELECT section_id 
                 FROM studentSection 
                 WHERE user_id = ?";

$sectionStmt = $conn->prepare($sectionQuery);
$sectionStmt->bind_param("i", $userId);
$sectionStmt->execute();
$sectionResult = $sectionStmt->get_result();
$studentSection = $sectionResult->fetch_assoc();

// Check if a section was found
if (!$studentSection) {
    $_SESSION['error'] = "No section found for the student.";
    header("Location: ../student/announcement.php");
    exit();
}

$sectionId = $studentSection['section_id'];

// Step 2: Fetch the subjects associated with the section
$subjectQuery = "SELECT id AS subject_id, subject 
                 FROM subject 
                 WHERE section_id = ?";

$subjectStmt = $conn->prepare($subjectQuery);
$subjectStmt->bind_param("i", $sectionId);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();

// Initialize an array to store tasks
$tasks = [];

while ($subjectRow = $subjectResult->fetch_assoc()) {
    $subjectId = $subjectRow['subject_id'];
    $subjectName = $subjectRow['subject'];

    // Step 3: Fetch tasks associated with the subject
    $taskQuery = "SELECT id, task_title, content, image_path, deadline 
                  FROM task 
                  WHERE subject_id = ? AND status = 'active'";

    $taskStmt = $conn->prepare($taskQuery);
    $taskStmt->bind_param("i", $subjectId);
    $taskStmt->execute();
    $taskResult = $taskStmt->get_result();

    // Add tasks to the array
    while ($taskRow = $taskResult->fetch_assoc()) {
        $taskId = $taskRow['id'];

        // Step 4: Fetch the student's answer and feedback for the task
        $answerQuery = "SELECT text_answer, image_path, feedback 
                        FROM taskAnswer 
                        WHERE task_id = ? AND student_id = ?";
        
        $answerStmt = $conn->prepare($answerQuery);
        $answerStmt->bind_param("ii", $taskId, $userId);
        $answerStmt->execute();
        $answerResult = $answerStmt->get_result();
        $answer = $answerResult->fetch_assoc();
        
        // Add tasks and answers to the array
        $tasks[] = [
            'subject' => $subjectName,
            'task_title' => $taskRow['task_title'],
            'content' => $taskRow['content'],
            'image_path' => $taskRow['image_path'],
            'deadline' => $taskRow['deadline'],
            'id' => $taskId,
            'student_answer_text' => $answer['text_answer'] ?? null,
            'student_answer_image' => $answer['image_path'] ?? null,
            'feedback' => $answer['feedback'] ?? null
        ];
    }
}

// Close statements and connection
$sectionStmt->close();
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
    <title>Student Task</title>
</head>
<body>
<div class="navbar">
    <a href="../student/announcement.php">Announcement</a>
    <a href="../student/admin_module.php">Admin Module</a>
    <a href="../student/task.php" style="color: wheat;">Task</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
</div>

<div class="container">

<table>
    <thead>
        <tr>
            <th>Subject</th>
            <th>Task Title</th>
            <th>Task Details</th>
            <th>Deadline</th>
            <th>Action</th>
            <th>Answer</th>
            <th>Feedback</th>
            <th>Delete Answer</th> <!-- New column for the delete button -->
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['subject']) ?></td>
                    <td><?= htmlspecialchars($task['task_title']) ?></td>
                    <td>
                        <?php if (!empty($task['content'])): ?>
                            <?= htmlspecialchars($task['content']) ?>
                        <?php elseif (!empty($task['image_path'])): ?>
                            <img src="../<?= htmlspecialchars($task['image_path']) ?>" alt="Task Image" style="max-width: 200px; height: auto;">
                        <?php else: ?>
                            No details available.
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($task['deadline']) ?></td>
                    <td>
                        <!-- Conditionally disable the submit button if an answer already exists -->
                        <button class="button openModal" 
                                data-task-id="<?= htmlspecialchars($task['id']) ?>"
                                <?= !empty($task['student_answer_text']) || !empty($task['student_answer_image']) ? 'disabled' : '' ?>>
                            Submit Answer
                        </button>
                    </td>
                    <td>
                        <!-- Display the student's answer (either text or image) -->
                        <?php if (!empty($task['student_answer_image'])): ?>
                            <img src="../../uploads/answer/<?= htmlspecialchars($task['student_answer_image']) ?>" alt="Submitted Image" style="max-width: 200px; height: auto;">
                        <?php elseif (!empty($task['student_answer_text'])): ?>
                            <?= htmlspecialchars($task['student_answer_text']) ?>
                        <?php else: ?>
                            No answer submitted.
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Display feedback -->
                        <?= !empty($task['feedback']) ? htmlspecialchars($task['feedback']) : 'No feedback yet.' ?>
                    </td>
                    <td>
                        <!-- Add Delete button if the answer exists -->
                        <?php if (!empty($task['student_answer_text']) || !empty($task['student_answer_image'])): ?>
                            <form method="post" action="../controller/StudentController/delete_answer.php" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['id']) ?>">
                                <input type="hidden" name="student_id" value="<?= htmlspecialchars($userId) ?>"> <!-- Student ID from session -->
                                <button type="submit" class="button delete-btn">Delete</button>
                            </form>
                        <?php else: ?>
                            No answer to delete.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No tasks found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


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
</script>

<script>
    document.querySelectorAll('.openModal').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('accountModal').style.display = 'flex';
            
            // Set the task ID in the hidden input
            const taskId = this.getAttribute('data-task-id');
            document.getElementById('taskId').value = taskId;  // Set hidden input value
        });

        
        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('accountModal').style.display = 'none';
        });
    });
</script>

<!---- Sweet Alert ---->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
      document.addEventListener('DOMContentLoaded', function () {
          // Check for success message
          <?php if (isset($_SESSION['success'])): ?>
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: '<?php echo $_SESSION['success']; ?>',
                  confirmButtonText: 'OK'
              });
              <?php unset($_SESSION['success']); // Clear the session variable ?>
          <?php endif; ?>

          // Check for error message
          <?php if (isset($_SESSION['error'])): ?>
              Swal.fire({
                  icon: 'error',
                  title: 'Oops...',
                  text: '<?php echo $_SESSION['error']; ?>',
                  confirmButtonText: 'Try Again'
              });
              <?php unset($_SESSION['error']); // Clear the session variable ?>
          <?php endif; ?>
      });
    </script>

</body>
</html>