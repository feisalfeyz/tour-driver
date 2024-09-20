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

// Get form data
$submitted_username = $_POST['username'] ?? '';
$submitted_password = $_POST['password'] ?? '';

if (empty($submitted_username) || empty($submitted_password)) {
    $error = "Username and password are required.";
} else {
    // Create database connection
    $conn = get_db_connection();

    // Check if username already exists
    $sql = "SELECT id FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $submitted_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        // Hash the password
        $password_hash = password_hash($submitted_password, PASSWORD_DEFAULT);

        // Insert new admin into the database
        $sql = "INSERT INTO admin (username, password_hash, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $submitted_username, $password_hash);

        if ($stmt->execute()) {
            // Registration successful
            header("Location: register_success.html");
            exit();
        } else {
            $error = "An error occurred. Please try again.";
        }
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h3 mb-3 fw-normal">Registration Error</h2>
        <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
        <a href="register.html" class="btn btn-primary">Go back to registration</a>
    </main>
</body>
</html>
