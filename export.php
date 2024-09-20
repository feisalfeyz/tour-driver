<?php
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

// Output headers to prompt for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="items_export.csv"');

// Output CSV data
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['Item ID', 'User ID', 'Item Name', 'Location', 'Color', 'Description', 'Picture URL', 'Created At', 'Item Type']);

// Fetch data from database
$sql = "SELECT item_id, user_id, item_name, location, color, description, picture_url, created_at, item_type FROM items";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// Close the connection and the file
fclose($output);
$conn->close();
exit();
?>
