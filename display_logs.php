<?php
// Database configuration
$host = 'localhost';
$dbname = 'your_database';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch and display log entries
function display_logs() {
    global $conn;

    // Prepare SQL statement
    $sql = "SELECT id, timestamp, user_id, action_type, description, ip_address, additional_info FROM admin_logs ORDER BY timestamp DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1'>
              <tr>
                <th>ID</th>
                <th>Timestamp</th>
                <th>User ID</th>
                <th>Action Type</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Additional Info</th>
              </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['action_type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ip_address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['additional_info']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No log entries found.";
    }

    // Close result set
    $result->free();
}

// Display the logs
display_logs();

// Close connection
$conn->close();
?>
