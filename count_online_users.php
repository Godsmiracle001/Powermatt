<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6th";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Count online users
$result = $conn->query("SELECT COUNT(*) as count FROM online_users");
$row = $result->fetch_assoc();
echo $row['count'];

$conn->close();
?>
