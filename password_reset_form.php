<?php
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

$token = $_GET['token'] ?? '';
$error = '';

if (!empty($token)) {
    $conn = get_db_connection();

    // Validate the token
    $sql = "SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, show the password reset form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['password'] ?? '';

            if (!empty($new_password)) {
                // Get user email from the token
                $row = $result->fetch_assoc();
                $email = $row['email'];

                // Hash the new password
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password
                $sql = "UPDATE admin SET password_hash = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $password_hash, $email);
                $stmt->execute();

                // Delete the password reset token
                $sql = "DELETE FROM password_resets WHERE token = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $token);
                $stmt->execute();

                // Password reset successful
                header("Location: password_reset_success.html");
                exit();
            } else {
                $error = "Password cannot be empty.";
            }
        }
    } else {
        $error = "Invalid or expired token.";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    $error = "No token provided.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="text-center">
    <main class="form-signin w-100 m-auto">
        <h1 class="h3 mb-3 fw-normal">Reset Password</h1>
        <?php if ($error): ?>
            <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
            <form action="password_reset_form.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="New Password" required>
                    <label for="floatingPassword">New Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
