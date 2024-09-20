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

    // Prepare SQL statement to fetch the admin record
    $sql = "SELECT id, username, password_hash FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $submitted_username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if an admin with the provided username exists
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verify password
        if (password_verify($submitted_password, $admin['password_hash'])) {
            // Start a session and store admin information
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];

            // Redirect to the admin dashboard or home page
            header("Location: dash.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign-In Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h3 mb-3 fw-normal">Sign-In Error</h2>
        <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
        <a href="signin.html" class="btn btn-primary">Go back to login</a>
    </main>
</body>
</html>
