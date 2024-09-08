<?php
include 'db_connect.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

// Output data for each row
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["first_name"] . "</td>";
        echo "<td>" . $row["last_name"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["lrn"] . "</td>";
        echo "<td>" . $row["parent_name"] . "</td>";
        echo "<td>" . $row["parent_email"] . "</td>";
        echo "<td>" . $row["parent_contact"] . "</td>";
        echo "<td>" . $row["section"] . "</td>";
        echo "<td>";
        echo '<a href="view-users.php?id=' . $row["id"] . '" class="btn btn-primary btn-sm">View</a> ';
        echo '<a href="update-users.php?id=' . $row["id"] . '" class="btn btn-success btn-sm">Update</a> ';
        echo '<a href="delete-users.php?id=' . $row["id"] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this teacher?\')">Delete</a>';
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='9' class='text-center'>No Registered Users Found</td></tr>";
}

// Close the database connection
$conn->close();

