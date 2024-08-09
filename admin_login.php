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

// Check login credentials
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminUsername = $_POST['username'];
    $adminPassword = $_POST['password']; // In a real application, use hashed passwords

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? AND password = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $adminUsername, $adminPassword);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Admin credentials are correct
        $stmt->bind_result($adminId);
        $stmt->fetch();
        $_SESSION['admin_id'] = $adminId;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Invalid credentials
        echo "<p>Invalid credentials. Please try again.</p>";
    }

    $stmt->close();
}

$conn->close();
?>
