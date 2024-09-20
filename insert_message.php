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

// Collect form data
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];
$message = $_POST['message'];
$agree_conditions = isset($_POST['agree_conditions']) ? 1 : 0;

// Prepare and execute the insert statement
$stmt = $conn->prepare("INSERT INTO message (full_name, email, phone_number, message, agree_conditions) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $full_name, $email, $phone_number, $message, $agree_conditions);

if ($stmt->execute()) {
    echo "Record inserted successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Redirect to a feedback page or display a message
// Assume form processing and validation happens here

// Prepare feedback message
$feedback = "Your submission was successful.";

// Redirect to index.php with feedback message
header("Location: index.php?message=" . urlencode($feedback));
exit();

?>
