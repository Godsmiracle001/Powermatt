<?php
// Database configuration
$servername = "localhost"; // Update if your server is different
$username = "root"; // Update with your database username
$password = ""; // No password for the database
$dbname = "6th"; // Use the database name '6th'

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect and sanitize form data
$fullName = htmlspecialchars(trim($_POST['fullName']));
$email = htmlspecialchars(trim($_POST['email']));
$password = htmlspecialchars(trim($_POST['password']));
$confirmPassword = htmlspecialchars(trim($_POST['confirmPassword']));

// Basic validation
if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
    echo "All fields are required.";
    exit;
}

if ($password !== $confirmPassword) {
    echo "Passwords do not match.";
    exit;
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullName, $email, $hashedPassword);

// Execute the statement
if ($stmt->execute()) {
    echo "Sign up successful!";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
