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

// Collect form data (with basic validation)
$full_name = !empty($_POST['full_name']) ? trim($_POST['full_name']) : null;
$email = !empty($_POST['email']) ? trim($_POST['email']) : null;
$phone_number = !empty($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
$item_name = !empty($_POST['item_name']) ? trim($_POST['item_name']) : null;
$location = !empty($_POST['location']) ? trim($_POST['location']) : null;
$color = !empty($_POST['color']) ? trim($_POST['color']) : '#000000'; // default color
$description = !empty($_POST['description']) ? trim($_POST['description']) : null;
$remember_me = isset($_POST['remember_me']) ? 1 : 0;
$item_type = 'lost'; // Since this is the lost item form

// Basic form validation
if (!$full_name || !$email || !$phone_number || !$item_name || !$location || !$description) {
    die("Please fill in all required fields.");
}

// Handle file upload
$picture_url = null;
if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['picture']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

    // Validate file extension
    if (!in_array($file_ext, $allowed_exts)) {
        die("Invalid file type. Please upload a JPG, JPEG, PNG, or GIF.");
    }

    $new_file_name = uniqid() . '.' . $file_ext; // Generate a unique name to avoid overwriting
    $upload_file = $upload_dir . $new_file_name;

    // Move file to upload directory
    if (move_uploaded_file($_FILES['picture']['tmp_name'], $upload_file)) {
        $picture_url = $upload_file;
    } else {
        die("File upload failed.");
    }
}

// Insert data into the Users table (assuming a Users table exists)
$stmt = $conn->prepare("INSERT INTO Users (full_name, email_address, phone_number, remember_me) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $full_name, $email, $phone_number, $remember_me);
$stmt->execute();
$user_id = $stmt->insert_id; // Get the ID of the inserted user
$stmt->close();

// Insert data into the Items table
$stmt = $conn->prepare("INSERT INTO Items (user_id, item_name, location, color, description, picture_url, item_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $user_id, $item_name, $location, $color, $description, $picture_url, $item_type);
$stmt->execute();
$stmt->close();

// Close connection
$conn->close();

echo "Form submitted successfully!";

// Redirect with feedback (optional)
$feedback = "Your submission was successful.";
header("Location: index.php?message=" . urlencode($feedback));
exit();
?>
