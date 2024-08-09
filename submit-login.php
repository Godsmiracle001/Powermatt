<?php
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

// Collect and sanitize form data
$email = htmlspecialchars(trim($_POST['email']));
$password = htmlspecialchars(trim($_POST['password']));

// Prepare and bind
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);

// Execute the statement
$stmt->execute();
$stmt->store_result();

// Check if email exists
if ($stmt->num_rows == 0) {
    echo "No account found with that email.";
    exit;
}

$stmt->bind_result($userId, $hashedPassword);
$stmt->fetch();

// Verify password
if (password_verify($password, $hashedPassword)) {
    session_start();
    $_SESSION['user_id'] = $userId;
    echo "Login successful!";
    header("Location: dashboard.php"); // Redirect to a protected page
} else {
    echo "Invalid password.";
}

// Close connections
$stmt->close();
$conn->close();
?>
