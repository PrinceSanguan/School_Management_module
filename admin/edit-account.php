<?php
include "../database/database.php";

session_start();

// Check if the user is an admin
if (!isset($_SESSION['userRole']) || $_SESSION['userRole'] !== 'admin') {
    $_SESSION['error'] = "You do not have permission to access this page!.";
    header("Location: ../index.php");
    exit();
}

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    // Fetch user details and role, including additional fields from studentLrn
    $query = "
        SELECT u.*, s.lrn, s.parent, s.address, s.number 
        FROM users u 
        LEFT JOIN studentLrn s ON u.id = s.user_id 
        WHERE u.id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        die("User not found.");
    }
    
    // Update user details
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $lrn = isset($_POST['lrn']) ? $_POST['lrn'] : null;
        $parent = isset($_POST['parent']) ? $_POST['parent'] : null;
        $address = isset($_POST['address']) ? $_POST['address'] : null;
        $number = isset($_POST['number']) ? $_POST['number'] : null;
        
        // Update user details
        $updateQuery = "UPDATE users SET firstName = ?, lastName = ?, phone = ?, email = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userId);
        
        if ($updateStmt->execute()) {
            // If the user is a student, update LRN and additional fields
            if ($user['userRole'] == 'student') {
                // Update LRN if provided
                if ($lrn !== null) {
                    $lrnUpdateQuery = "UPDATE studentLrn SET lrn = ?, parent = ?, address = ?, number = ? WHERE user_id = ?";
                    $lrnStmt = $conn->prepare($lrnUpdateQuery);
                    $lrnStmt->bind_param("ssssi", $lrn, $parent, $address, $number, $userId);
                    $lrnStmt->execute();
                }
            }
            
            $_SESSION['success'] = 'The account has been updated!';
            header("Location: ../admin/account-approval.php");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../asset/css/account-approval.css">
    <title>Edit User</title>
    <style>
      @import url(https://fonts.googleapis.com/css?family=Roboto:300);

.login-page {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}
.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: 360px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;
  box-sizing: border-box;
  font-size: 14px;
}
.form button {
  font-family: "Roboto", sans-serif;
  text-transform: uppercase;
  outline: 0;
  background: #4CAF50;
  width: 100%;
  border: 0;
  padding: 15px;
  color: #FFFFFF;
  font-size: 14px;
  -webkit-transition: all 0.3 ease;
  transition: all 0.3 ease;
  cursor: pointer;
}
.form button:hover,.form button:active,.form button:focus {
  background: #43A047;
}
.form .message {
  margin: 15px 0 0;
  color: #b3b3b3;
  font-size: 12px;
}
.form .message a {
  color: #4CAF50;
  text-decoration: none;
}
.form .register-form {
  display: none;
}
.container {
  position: relative;
  z-index: 1;
  max-width: 300px;
  margin: 0 auto;
}
.container:before, .container:after {
  content: "";
  display: block;
  clear: both;
}
.container .info {
  margin: 50px auto;
  text-align: center;
}
.container .info h1 {
  margin: 0 0 15px;
  padding: 0;
  font-size: 36px;
  font-weight: 300;
  color: #1a1a1a;
}
.container .info span {
  color: #4d4d4d;
  font-size: 12px;
}
.container .info span a {
  color: #000000;
  text-decoration: none;
}
.container .info span .fa {
  color: #EF3B3A;
}
body {
  background: #76b852; /* fallback for old browsers */
  background: rgb(141,194,111);
  background: linear-gradient(90deg, rgba(141,194,111,1) 0%, rgba(118,184,82,1) 50%);
  font-family: "Roboto", sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;      
}
    </style>
</head>
<body>
    
<div class="login-page">
  <div class="form">
  <h2>Edit User</h2>
    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>" required><br>
        <label>Last Name:</label>
        <input type="text" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>" required><br>
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
        <?php if ($user['userRole'] == 'student'): ?>
            <label>LRN:</label>
            <input type="text" name="lrn" value="<?php echo htmlspecialchars($user['lrn']); ?>"><br>
            <input type="text" name="parent" value="<?php echo htmlspecialchars($user['parent']); ?>"><br>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>"><br>
            <input type="number" name="number" value="<?php echo htmlspecialchars($user['number']); ?>"><br>
        <?php endif; ?>
        <input type="submit" value="Update">
    </form>
  </div>
</div>


<script>
  $('.message a').click(function(){
   $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
});
</script>
</body>
</html>