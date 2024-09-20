<?php
session_start();

// Redirect to sign-in page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: signin.html");
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'tourdriver';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get search query if present
$search_query = isset($_GET['search']) ? '%' . $conn->real_escape_string($_GET['search']) . '%' : '';

// Function to fetch and display log entries
function display_logs() {
    global $conn, $search_query;

    // Prepare SQL statement with search functionality
    $sql = "SELECT id, timestamp, user_id, action_type, description, ip_address, additional_info 
            FROM admin_logs 
            WHERE description LIKE ? 
            ORDER BY timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table class='table table-striped table-sm'>
              <thead>
                <tr>
                  <th scope='col'>ID</th>
                  <th scope='col'>Timestamp</th>
                  <th scope='col'>User ID</th>
                  <th scope='col'>Action Type</th>
                  <th scope='col'>Description</th>
                  <th scope='col'>IP Address</th>
                  <th scope='col'>Additional Info</th>
                </tr>
              </thead>
              <tbody>";
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
        echo "</tbody></table>";
    } else {
        echo "<p>No log entries found.</p>";
    }
    $stmt->close();
}

// Function to fetch and display items
function display_items() {
    global $conn, $search_query;

    // Prepare SQL statement with search functionality
    $sql = "SELECT item_id, user_id, item_name, location, color, description, picture_url, created_at, item_type 
            FROM items 
            WHERE item_name LIKE ? OR description LIKE ? 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Items</h2>
              <div class='table-responsive'>
              <table class='table table-striped table-sm'>
              <thead>
                <tr>
                  <th scope='col'>Item ID</th>
                  <th scope='col'>User ID</th>
                  <th scope='col'>Item Name</th>
                  <th scope='col'>Location</th>
                  <th scope='col'>Color</th>
                  <th scope='col'>Description</th>
                  <th scope='col'>Picture URL</th>
                  <th scope='col'>Created At</th>
                  <th scope='col'>Item Type</th>
                </tr>
              </thead>
              <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['item_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
            echo "<td>" . htmlspecialchars($row['color']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['picture_url']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "<td>" . htmlspecialchars($row['item_type']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table></div>";
    } else {
        echo "<p>No items found.</p>";
    }
    $stmt->close();
}

// Function to fetch and display messages
function display_messages() {
    global $conn, $search_query;

    // Prepare SQL statement with search functionality
    $sql = "SELECT id, created_at, phone_number, full_name, email, message 
            FROM message 
            WHERE full_name LIKE ? OR email LIKE ? OR message LIKE ? 
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $search_query, $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Messages</h2>
              <div class='table-responsive'>
              <table class='table table-striped table-sm'>
              <thead>
                <tr>
                  <th scope='col'>#</th>
                  <th scope='col'>Name</th>
                  <th scope='col'>Email</th>
                  <th scope='col'>Mobile</th>
                  <th scope='col'>Message</th>
                </tr>
              </thead>
              <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['message']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table></div>";
    } else {
        echo "<p>No messages found.</p>";
    }
    $stmt->close();
}

// Display the logs, items, and messages
display_logs();
display_items();
display_messages();

// Close connection
$conn->close();
?>
