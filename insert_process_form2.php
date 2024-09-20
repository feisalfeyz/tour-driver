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

// Collect and sanitize form data
$full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
$item_name = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
$location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
$color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$remember_me = isset($_POST['remember_me']) ? 1 : 0;

// Validate important fields
if (!$full_name || !$email || !$phone_number || !$item_name || !$location || !$description) {
    die("All fields are required.");
}

// Handle file upload securely
$picture_url = null;
if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $file_info = pathinfo($_FILES['picture']['name']);
    $file_ext = strtolower($file_info['extension']);
    $file_size = $_FILES['picture']['size'];

    // Validate file type and size
    if (!in_array($file_ext, $allowed_types)) {
        die("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
    }
    if ($file_size > 2000000) { // 2MB limit
        die("File size exceeds the 2MB limit.");
    }

    // Create unique filename to avoid overwriting
    $upload_dir = 'uploads/';
    $new_filename = uniqid() . '.' . $file_ext;
    $upload_file = $upload_dir . $new_filename;

    if (!move_uploaded_file($_FILES['picture']['tmp_name'], $upload_file)) {
        die("File upload failed.");
    }
    $picture_url = $upload_file;
}

// Insert data into Users table using prepared statements
$stmt = $conn->prepare("INSERT INTO Users (full_name, email_address, phone_number, remember_me) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $full_name, $email, $phone_number, $remember_me);

if (!$stmt->execute()) {
    die("Error inserting into Users table: " . $stmt->error);
}
$user_id = $stmt->insert_id;
$stmt->close();

// Insert data into Items table
$stmt = $conn->prepare("INSERT INTO Items (user_id, item_name, location, color, description, picture_url, item_type) VALUES (?, ?, ?, ?, ?, ?, 'found')");
$stmt->bind_param("isssss", $user_id, $item_name, $location, $color, $description, $picture_url);

if (!$stmt->execute()) {
    die("Error inserting into Items table: " . $stmt->error);
}
$stmt->close();

// Search for similar records using prepared statements
$similar_query = "SELECT * FROM Items WHERE item_type = 'found' AND 
                  (SOUNDEX(item_name) = SOUNDEX(?) 
                   OR description LIKE ? 
                   OR location = ?)";
$like_description = '%' . $description . '%';
$stmt = $conn->prepare($similar_query);
$stmt->bind_param("sss", $item_name, $like_description, $location);
$stmt->execute();
$result = $stmt->get_result();

// Display similar records if found
if ($result->num_rows > 0) {
    echo "<h2>Similar Records Found:</h2>";
    echo "<table border='1'>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Found At</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No similar records found.";
}

// Close connection
$conn->close();

// Redirect to index.php with feedback message
$feedback = "Your submission was successful!";
header("Location: index.php?message=" . urlencode($feedback));
exit();
?>
