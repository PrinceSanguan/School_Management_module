<tbody>
    <?php
    include 'db_connect.php';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL query
    $sql = "SELECT id, image, title, date, section, description FROM modules";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Escape output to prevent XSS attacks
            $id = htmlspecialchars($row['id']);
            $image = htmlspecialchars($row['image']);
            $title = htmlspecialchars($row['title']);
            $date = htmlspecialchars($row['date']);
            $section = htmlspecialchars($row['section']);
            $description = htmlspecialchars($row['description']);

            echo "<tr>";
            echo "<td>{$id}</td>";
            echo "<td><img src='modules/{$image}' alt='Module Image' style='max-width: 100px; max-height: 100px; object-fit: cover;'></td>";
            echo "<td>{$title}</td>";
            echo "<td>{$date}</td>";
            echo "<td>{$section}</td>";
            echo "<td>{$description}</td>";
            echo "<td>
                    <a href='update-module.php?id={$id}' class='btn btn-success btn-sm'>Update</a>
                    <a href='delete-module.php?id={$id}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this module?\")'>Delete</a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr class='text-center'><td colspan='7'>No modules found</td></tr>";
    }

    // Close connection
    $conn->close();
    ?>
</tbody>
