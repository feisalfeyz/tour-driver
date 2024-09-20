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
$item_name = $_POST['item_name'];
$location = $_POST['location'];
$color = $_POST['color'];
$description = $_POST['description'];
$remember_me = isset($_POST['remember_me']) ? 1 : 0;

// Handle file upload
$picture_url = null;
if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . basename($_FILES['picture']['name']);
    if (move_uploaded_file($_FILES['picture']['tmp_name'], $upload_file)) {
        $picture_url = $upload_file;
    } else {
        echo "File upload failed.";
        exit();
    }
}

// Insert data into Users table
$stmt = $conn->prepare("INSERT INTO Users (full_name, email_address, phone_number, remember_me) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $full_name, $email, $phone_number, $remember_me);
$stmt->execute();
$user_id = $stmt->insert_id; // Get the ID of the inserted user
$stmt->close();

// Insert data into Items table
$stmt = $conn->prepare("INSERT INTO Items (user_id, item_name, location, color, description, picture_url, item_type) VALUES (?, ?, ?, ?, ?, ?, 'lost')");
$stmt->bind_param("isssss", $user_id, $item_name, $location, $color, $description, $picture_url);
$stmt->execute();
$stmt->close();

// Search for similar records using LIKE or SOUNDEX
$similar_query = "SELECT * FROM Items 
                  WHERE SOUNDEX(item_name) = SOUNDEX('$item_name')
                  AND item_type = 'found'
                  OR description LIKE '%$description%' 
                  OR location = '$location'";

$result = $conn->query($similar_query);

// Display similar records
if ($result->num_rows > 0) {
    echo "<h2>Similar Records Found:</h2>";
    echo "<table border='1'>
          <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>Found At</th>
          </tr>";

    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>" . $row['location'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No similar records found.";
}

// Close connection
$conn->close();

echo "Form submitted successfully!";

// Assume form processing and validation happens here

// Prepare feedback message
$feedback = "Your submission was successful.";

// Redirect to index.php with feedback message
//header("Location: index.php?message=" . urlencode($feedback));
exit();
?>
