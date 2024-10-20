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
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        #errorMessage {
            color: red;
            margin-top: 10px;
        }
    </style>
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
                            <?php if (!empty($answer['image_path'])): ?>
                                <img src="../../uploads/answer/<?= htmlspecialchars($answer['image_path']) ?>" alt="Submitted Image" style="max-width: 200px; height: auto;">
                            <?php elseif (!empty($answer['text_answer'])): ?>
                                <?= htmlspecialchars($answer['text_answer']) ?>
                            <?php else: ?>
                                No answer submitted.
                            <?php endif; ?>
                        </td>
                        <td>
                            <div id="feedback_display_<?= htmlspecialchars($answer['id']) ?>">
                                <?php if (!empty($answer['feedback'])): ?>
                                    <?= htmlspecialchars($answer['feedback']) ?>
                                <?php else: ?>
                                    No feedback yet.
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <button onclick="openFeedbackModal(<?= htmlspecialchars($answer['id']) ?>, <?= htmlspecialchars($taskId) ?>, <?= htmlspecialchars($answer['student_id']) ?>)" class="button">Submit Feedback</button>
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

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeFeedbackModal()">&times;</span>
        <h2>Submit Feedback</h2>
        <form id="feedbackForm" method="post">
            <input type="hidden" id="modal_task_id" name="task_id">
            <input type="hidden" id="modal_student_id" name="student_id">
            <input type="hidden" id="modal_answer_id" name="answer_id">
            
            <label for="modal_feedback">Feedback:</label>
            <textarea id="modal_feedback" name="feedback" rows="4" cols="50" required></textarea>
            
            <button type="submit" class="button">Submit Feedback</button>
        </form>
        <div id="errorMessage"></div>
    </div>
</div>

<script>
    function openFeedbackModal(answerId, taskId, studentId) {
        document.getElementById('feedbackModal').style.display = 'block';
        document.getElementById('modal_task_id').value = taskId;
        document.getElementById('modal_student_id').value = studentId;
        document.getElementById('modal_answer_id').value = answerId;
        document.getElementById('errorMessage').innerHTML = ''; // Clear any previous error messages
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModal').style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == document.getElementById('feedbackModal')) {
            closeFeedbackModal();
        }
    }

    // Handle form submission
    document.getElementById('feedbackForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        fetch('submit_feedback.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the feedback display
                document.getElementById('feedback_display_' + formData.get('answer_id')).innerText = formData.get('feedback');
                closeFeedbackModal();
            } else {
                // Display error message
                document.getElementById('errorMessage').innerHTML = 'Error: ' + (data.message || 'An unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('errorMessage').innerHTML = 'An error occurred while submitting feedback. Please try again.';
        });
    };
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