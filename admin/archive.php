<?php
include "../database/database.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userId']) || !isset($_SESSION['userRole'])) {
    $_SESSION['error'] = "Please log in to access this page.";
    header("Location: ../../login.php");
    exit();
}

// Check if the user is an admin or the verified user
if ($_SESSION['userRole'] !== 'admin' && $_SESSION['userId'] != $subject['userId']) {
    $_SESSION['error'] = "You do not have permission to access this page.";
    header("Location: ../../index.php");
    exit();
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to get archived users
$query = "
    SELECT u.id, u.userRole, CONCAT(u.firstName, ' ', u.lastName) AS fullName
    FROM users u
    WHERE u.is_archived = 1
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <title>Archived Users</title>
</head>
<body>
    
<div class="navbar">
    <a href="../admin/account-approval.php">Accounts</a>
    <a href="../admin/section.php">Section</a>
    <a href="../admin/announcement.php">Announcement</a>
    <a href="../admin/registration.php">Registration</a>
    <a href="../admin/student-registration.php">Student Registration</a>
    <a href="../admin/calendar.php">Calendar</a>
    <a href="../admin/archive.php" style="color:wheat">Archive</a>
    <a href="../controller/LogoutController/logOut.php">Logout</a>
</div>

<div class="container">
    <h1>Archived Accounts</h1>

    <!-- Table to display archived users -->
    <table id="myTable">
        <thead>
            <tr>
                <th>User Role</th>
                <th>Full Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['userRole']); ?></td>
                        <td><?php echo htmlspecialchars($row['fullName']); ?></td>
                        <td>
                            <button onclick="unarchiveUser(<?php echo $row['id']; ?>)" style="background-color: green; color: white; border: none; padding: 10px 20px; cursor: pointer;">Unarchive</button>
                            <button onclick="permanentDeleteUser(<?php echo $row['id']; ?>)" style="background-color: red; color: white; border: none; padding: 10px 20px; cursor: pointer;">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- You can use a placeholder row here but the `colspan="3"` must match the number of <th> elements -->
                <tr>
                    <td style="text-align: center;">No archived users found.</td>
                    <td style="text-align: center;">No archived users found.</td>
                    <td style="text-align: center;">No archived users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTables with an empty table message
        $('#myTable').DataTable({
            "language": {
                "emptyTable": "No archived users available"
            }
        });

        // Check for success message
        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        // Check for error message
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'Try Again'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    });

    function unarchiveUser(id) {
        if (confirm('Are you sure you want to restore this user?')) {
            window.location.href = '../controller/AdminController/unarchive_user.php?id=' + id; // Redirect to unarchive script
        }
    }

    function permanentDeleteUser(id) {
        if (confirm('Are you sure you want to permanently delete this user?')) {
            window.location.href = '../controller/AdminController/permanent_delete_user.php?id=' + id; // Redirect to delete script
        }
    }
</script>

</body>
</html>
