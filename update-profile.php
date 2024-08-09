<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6th";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$userId = $_SESSION['user_id'];

// Handle profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);
    move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile);
    $profilePicture = basename($_FILES['profile_picture']['name']);
} else {
    // Use existing profile picture if no new file is uploaded
    $profilePicture = $_POST['existing_profile_picture'];
}

// Handle webcam image
if (isset($_POST['webcam_image'])) {
    $webcamImage = $_POST['webcam_image'];
    $webcamImageData = base64_decode(str_replace('data:image/png;base64,', '', $webcamImage));
    $uploadFile = 'uploads/' . uniqid() . '.png';
    file_put_contents($uploadFile, $webcamImageData);
    $profilePicture = basename($uploadFile);
}

// Prepare and execute the update query
$stmt = $conn->prepare("UPDATE users SET profile_picture = ?, username = ?, profession = ?, skills = ? WHERE id = ?");
$stmt->bind_param("ssssi", $profilePicture, $_POST['username'], $_POST['profession'], $_POST['skills'], $userId);

if ($stmt->execute()) {
    header("Location: dashboard.php?update=success");
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
