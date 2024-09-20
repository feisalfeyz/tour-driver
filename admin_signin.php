<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'tourdriver';
$username = 'root';
$password = '';

// Function to create a database connection
function get_db_connection() {
    global $host, $dbname, $username, $password;
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);

    // Create database connection
    $conn = get_db_connection();

    // Prepare and execute SQL statement to find the admin by username
    $sql = "SELECT id, password FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $stmt->store_result();

    // Check if username exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($input_password, $hashed_password)) {
            // Password is correct, start a session
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['username'] = $input_username;
            header("Location: dash.php"); // Redirect to admin dashboard
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Username not found.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
