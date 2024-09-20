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

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $conn = get_db_connection();

    // Check if the email exists in the database
    $sql = "SELECT id, username FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $username = $user['username'];

        // Generate a password reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('U') + 3600; // Token expires in 1 hour

        // Insert the token into the database
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $token, date('Y-m-d H:i:s', $expires));
        $stmt->execute();

        // Send password reset email
        $resetLink = "http://yourdomain.com/password_reset_form.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Hi $username,\n\nTo reset your password, please click the following link: $resetLink\n\nIf you did not request this, please ignore this email.";
        $headers = "From: no-reply@yourdomain.com\r\n";

        mail($email, $subject, $message, $headers);

        // Success message
        header("Location: password_reset_request_success.html");
        exit();
    } else {
        $error = "No account found with that email address.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h2 class="h3 mb-3 fw-normal">Password Reset Request Error</h2>
        <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
        <a href="password_reset_request.html" class="btn btn-primary">Go back</a>
    </main>
</body>
</html>
