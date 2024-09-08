<?php
include 'db_connect.php'; 

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users
$users_result = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users");

if (!$users_result) {
    die("Error fetching users: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subjects</title>
    <link rel="icon" href="images/logo.webp" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baskervville+SC&family=Nerko+One&family=New+Amsterdam&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            padding-top: 56px;
            font-family: "New Amsterdam", sans-serif;
            background-color: #0D1B2A;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand ms-5" href="#">School Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="update-admin.php">Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teacher-announcement.php">Teacher Announcement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="student-announcement.php">Student Announcement</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="view-subjects.php">View Subjects</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users-admin.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="teachers-admin.php">Teachers List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin-logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h2 class="mt-5">All Users and Their Subjects</h2>
        <a href="subjects.php" class="btn btn-primary mt-4">Add Subject</a>
        <?php while ($user = $users_result->fetch_assoc()): ?>
        <div class="mt-4">
            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
            <?php
            $user_id = $user['id'];
            $subjects_result = $conn->query("SELECT id, subject_name FROM subjects WHERE user_id = $user_id");

            if ($subjects_result && $subjects_result->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="editSubject(<?php echo $subject['id']; ?>, '<?php echo addslashes($subject['subject_name']); ?>')">Update</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteSubject(<?php echo $subject['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No subjects found for this user.</p>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1" aria-labelledby="editSubjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSubjectModalLabel">Edit Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editSubjectForm">
                        <input type="hidden" id="edit-subject-id" name="subject_id">
                        <div class="mb-3">
                            <label for="edit-subject-name" class="form-label">Subject Name</label>
                            <input type="text" class="form-control" id="edit-subject-name" name="subject_name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('editSubjectForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('update-subject.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(result => {
                  if (result.status === 'success') {
                      Swal.fire('Updated!', result.message, 'success').then(() => location.reload());
                  } else {
                      Swal.fire('Error!', result.message, 'error');
                  }
              }).catch(error => {
                  Swal.fire('Error!', 'An unexpected error occurred.', 'error');
              });
        });

        function deleteSubject(subjectId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this subject!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`delete-subject.php?id=${subjectId}`, {
                        method: 'GET'
                    }).then(response => response.json())
                      .then(result => {
                          if (result.status === 'success') {
                              Swal.fire('Deleted!', result.message, 'success').then(() => location.reload());
                          } else {
                              Swal.fire('Error!', result.message, 'error');
                          }
                      }).catch(error => {
                          Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                      });
                }
            });
        }

        function editSubject(subjectId, subjectName) {
            document.getElementById('edit-subject-id').value = subjectId;
            document.getElementById('edit-subject-name').value = subjectName;
            var myModal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
            myModal.show();
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
