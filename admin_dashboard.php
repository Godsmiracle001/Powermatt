<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit;
}

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

// Fetch check-in data
$sql = "
    SELECT u.email, u.age, u.profession, u.skills, d.checkin_date 
    FROM users u
    LEFT JOIN daily_checkins d ON u.id = d.user_id
    ORDER BY u.email, d.checkin_date DESC
";
$result = $conn->query($sql);

// Close connection
$conn->close();

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .checkin-table {
            margin-top: 20px;
        }
        .checkin-table th, .checkin-table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.html">6TH UNION - Admin</a>
        </nav>
    </header>

    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <table class="table table-bordered checkin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Check-In Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fullName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['checkin_date']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No check-in records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>Copyright &copy; 2023 6TH UNION Foundation. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
