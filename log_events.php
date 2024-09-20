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

// Function to log an event
function log_event($user_id, $action_type, $description, $ip_address = null, $additional_info = null) {
    global $conn;

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO admin_logs (user_id, action_type, description, ip_address, additional_info) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $action_type, $description, $ip_address, $additional_info);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Log entry added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Example usage of the log_event function
$user_id = 1; // Example user ID
$action_type = 'login'; // Example action type
$description = 'User logged in successfully';
$ip_address = $_SERVER['REMOTE_ADDR']; // User's IP address
$additional_info = 'N/A'; // Additional information

log_event($user_id, $action_type, $description, $ip_address, $additional_info);

// Close connection
$conn->close();
?>
