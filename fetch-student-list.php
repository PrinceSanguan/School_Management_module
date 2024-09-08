<?php
// Database configuration
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch student data
$sql = "SELECT id, first_name, last_name, p1_grade, p2_grade, p3_grade, image FROM students_grades";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";

        // Display the image in the second column
        if (!empty($row['image'])) {
            $image_path = htmlspecialchars($row['image']);
            echo "<td><img src='$image_path' alt='Student Image' style='max-width: 100px; max-height: 100px;'></td>";
        } else {
            echo "<td>No image</td>";
        }

        // Output other student data
        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['p1_grade']) . "</td>";
        echo "<td>" . htmlspecialchars($row['p2_grade']) . "</td>";
        echo "<td>" . htmlspecialchars($row['p3_grade']) . "</td>";
        
        // Actions column
        echo "<td>
                <a href='update-student-list.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-success btn-sm'>Update</a>
                <a href='delete-student-list.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a>
              </td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
}
$conn->close();

