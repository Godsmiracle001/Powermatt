<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "6th";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Remove user from online_users table
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM online_users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

session_destroy();
header("Location: login.html");
exit;
?>

<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to index page
header("Location: index.html");
exit;

