<?php
include "../database/database.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Get the user ID and role from the session
$userId = $_SESSION['userId'];
$userRole = $_SESSION['userRole'];

// Verify if the user is a teacher
if ($userRole !== 'teacher') {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

// Query to get subjects associated with the teacher
$query = "SELECT subject.id, subject.subject 
          FROM teacherSubject
          JOIN subject ON teacherSubject.subject_id = subject.id
          WHERE teacherSubject.user_id = ?";

// Prepare the SQL statement
$stmt = $conn->prepare($query);
if (!$stmt) {
    $_SESSION['error'] = "Error preparing statement: " . $conn->error;
    header("Location: ../../teacher/subject.php");
    exit();
}

// Bind the user ID parameter
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$subjects = []; // Initialize an array to store the fetched data

if ($result->num_rows > 0) {
    // Fetching results
    while ($row = $result->fetch_assoc()) {
        // Store both subject name and id
        $subjects[] = [
            'id' => $row['id'],
            'subject' => $row['subject']
        ];
    }
} else {
    // No subjects found for this teacher
    $_SESSION['error'] = "No subjects found for this teacher.";
}

$stmt->close();

// NEW LOGIC STARTS HERE

// Query to fetch tasks related to the subjects
$queryTasks = "SELECT task.id, subject.subject, task.task_title, task.content, task.image_path, task.deadline, task.status 
               FROM task
               JOIN subject ON task.subject_id = subject.id
               JOIN teacherSubject ON teacherSubject.subject_id = subject.id
               WHERE teacherSubject.user_id = ?";

$stmtTasks = $conn->prepare($queryTasks);
if (!$stmtTasks) {
    $_SESSION['error'] = "Error preparing task statement: " . $conn->error;
    header("Location: ../../teacher/tasks.php");
    exit();
}

// Bind the user ID parameter for fetching tasks
$stmtTasks->bind_param("i", $userId);
$stmtTasks->execute();
$resultTasks = $stmtTasks->get_result();

$tasks = []; // Initialize an array to store the fetched tasks
$currentDate = date("Y-m-d"); // Get the current date

if ($resultTasks->num_rows > 0) {
    // Fetching results
    while ($rowTask = $resultTasks->fetch_assoc()) {
        // Check if the deadline equals the current date
        if ($rowTask['deadline'] < $currentDate) {
            // Set the task status to inactive in the array
            $rowTask['status'] = 'inactive';

            // Update the task status in the database
            $updateQuery = "UPDATE task SET status = 'inactive' WHERE id = ?";
            $stmtUpdate = $conn->prepare($updateQuery);
            if ($stmtUpdate) {
                $stmtUpdate->bind_param("i", $rowTask['id']);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            } else {
                $_SESSION['error'] = "Error updating task status: " . $conn->error;
            }
        }
        $tasks[] = $rowTask; // Store each task row
    }
} else {
    // No tasks found
    $_SESSION['error'] = "No tasks found.";
}

$stmtTasks->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Task</title>
    <style>
        /* Modal styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content {
    position: relative;
    background-color: #fff;
    margin: 5% auto;
    padding: 25px;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: slideIn 0.3s ease-in-out;
}

/* Modal header */
.modal-content h2 {
    color: #2c3e50;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
    font-size: 24px;
}

/* Form elements styling */
.modal-content form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.modal-content select,
.modal-content input[type="text"],
.modal-content input[type="date"],
.modal-content textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.modal-content select:focus,
.modal-content input[type="text"]:focus,
.modal-content input[type="date"]:focus,
.modal-content textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
}

/* File input styling */
.modal-content input[type="file"] {
    padding: 10px;
    border: 2px dashed #ddd;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
}

/* Submit button */
.modal-content button[type="submit"] {
    background-color: #3498db;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: background-color 0.3s;
}

.modal-content button[type="submit"]:hover {
    background-color: #2980b9;
}

/* Close button */
.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 24px;
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 4px;
    transition: all 0.3s;
}

.modal-close:hover {
    color: #e74c3c;
    background-color: rgba(0, 0, 0, 0.05);
}

/* Labels */
.modal-content label {
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 5px;
    display: block;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsive design */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 10% auto;
        padding: 20px;
    }
    
    .modal-content h2 {
        font-size: 20px;
    }
}
    </style>
</head>
<body>
    <div class="navbar">
        <a href="../teacher/announcement.php">Announcement</a>
        <a href="../teacher/assign_subject.php">Subject</a>
        <a href="../teacher/task.php" style="color: wheat;">Task</a>
        <a href="../teacher/profile.php">Profile</a>
        <a href="../teacher/monitoring.php">Monitoring</a>
        <a href="../controller/LogoutController/logOut.php">Logout</a>
        <div class="burger">&#9776;</div>
    </div>

    <div class="container">
    
    <button class="button" id="openModal" style="margin-bottom: 10px;">Add Task</button>

    <table>
    <thead>
        <tr>
            <th>Subject</th>
            <th>Task Title</th>
            <th>Content</th>
            <th>Deadline</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($task['subject']); ?>
                    </td>
                    <td>
                        <a href="../controller/TeacherController/task_details.php?id=<?php echo urlencode($task['id']); ?>">
                            <?php echo htmlspecialchars($task['task_title']); ?>
                        </a>
                    </td>
                    <td>
                        <?php if (!empty($task['content'])): ?>
                            <?php echo htmlspecialchars($task['content']); ?>
                        <?php elseif (!empty($task['image_path'])): ?>
                            <?php
                            // Split multiple image paths by comma
                            $imagePaths = explode(',', $task['image_path']);
                            foreach ($imagePaths as $imagePath):
                                // Correct the image path to point to the right folder
                                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                                    $protocol = 'https://';
                                } else {
                                    $protocol = 'http://';
                                }
                                $correctedPath = $protocol . $_SERVER['HTTP_HOST'] . '/' . htmlspecialchars($imagePath);
                            ?>
                                <button onclick="viewImage('<?php echo $correctedPath; ?>')">View Image</button>
                                <br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            No content or image
                        <?php endif; ?>
                    </td>
                    <td><?php echo date("F j, Y", strtotime($task['deadline'])); ?></td>
                    <td><?php echo ucfirst(htmlspecialchars($task['status'])); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No tasks available</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<!---------------ADD TASK---------------------------->
<div class="modal" id="accountModal">
      <div class="modal-content">
        <button class="modal-close" id="closeModal">&times;</button>
        <h2>Add Task</h2>

        <form id="addAccountForm" method="post" action="../controller/TeacherController/add_task.php" enctype="multipart/form-data">

          <!-- This is for Subject Module -->
          <label>Subject:</label>
          <select name="subject" required>
              <option value="" disabled selected>Select Subject</option>
              <?php if (!empty($subjects)): ?>
                  <?php foreach ($subjects as $subject): ?>
                      <option value="<?php echo htmlspecialchars($subject['id']); ?>">  <!-- Ensure 'id' is the subject's id from DB -->
                          <?php echo htmlspecialchars($subject['subject']); ?>
                      </option>
                  <?php endforeach; ?>
              <?php else: ?>
                  <option value="" disabled>No subjects assigned</option>
              <?php endif; ?>
          </select>

          <!-- This is for task title -->
          <input type="text" name="task_title" placeholder="Title of your task" required>

            <!-- Select type of Content -->
            <select name="postType" id="postType">
              <option value="" disabled selected>Select Type</option>
              <option value="Image">Image</option>
              <option value="Text">Text</option>
            </select>

          <!-- Show this image if this is select hide if not -->
          <div id="imageUpload" style="display: none;">
            <label>Upload Task:</label>
            <input type="file" name="image[]" id="imageFile" accept=".png, .jpg, .jpeg, pdf" multiple>
          </div>

          <!-- Show this textarea if this is select hide if not -->
          <div id="textInput" style="display: none;">
            <label>Write your task</label>
            <textarea name="content" rows="5" cols="65" placeholder="Place your task here"></textarea>
          </div>
          
          <label>Set Deadline:</label>
          <input type="date" name="deadline" id="deadline">

          <button type="submit">Add Task</button>
        </form>
      </div>
    </div>
     <!---------------ADD TASK---------------------------->

     <!---------------ADD TASK MODAL---------------------------->
     <script>
    document.getElementById('openModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'flex';
    });

    document.getElementById('closeModal').addEventListener('click', function() {
      document.getElementById('accountModal').style.display = 'none';
    });

    window.addEventListener('click', function(event) {
      if (event.target === document.getElementById('accountModal')) {
        document.getElementById('accountModal').style.display = 'none';
      }
    });
  </script>
   <!---------------ADD TASK MODAL---------------------------->
  
    <!---- Sweet Alert ---->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {

    // Toggle burger menu visibility
    const burger = document.querySelector('.burger');
    const navbar = document.querySelector('.navbar');
        burger.addEventListener('click', function () {
            navbar.classList.toggle('active');
        });

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

<!--script for view image -->
<script>
    function viewImage(imagePath) {
        window.open(imagePath, '_blank'); // Opens the image in a new tab
    }
</script>
<!--script for view image -->

<!--script for the date ---->
<script>
// Get the input element
const deadlineInput = document.getElementById('deadline');

// Set the minimum date to tomorrow
const today = new Date();
today.setDate(today.getDate() + 1); // Add one day to get tomorrow
deadlineInput.min = today.toISOString().split('T')[0];
</script>
<!--script for the date ---->

<!-- Script for hiding text and image-->
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
<!-- Script for hiding text and image-->

</body>
</html>