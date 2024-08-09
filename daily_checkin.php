<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$userId = $_SESSION['user_id'];
$date = date('Y-m-d');

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

// Check if user has already checked in today
$stmt = $conn->prepare("SELECT COUNT(*) FROM daily_checkins WHERE user_id = ? AND checkin_date = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("is", $userId, $date);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count == 0) {
    // Insert check-in record
    $stmt = $conn->prepare("INSERT INTO daily_checkins (user_id, checkin_date) VALUES (?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("is", $userId, $date);
    if ($stmt->execute()) {
        echo "Check-in successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "You have already checked in today.";
}

$conn->close();
?>
