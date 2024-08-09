<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
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

// Get user data
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT email, username, profession, skills, profile_picture FROM users WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close connections
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .user-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            position: relative;
        }
        .user-info img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
        }
        .user-info p {
            margin: 5px 0;
        }
        .online-status {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .online-status span {
            margin-right: 10px;
        }
        .online-status .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: green;
            display: inline-block;
        }
        .online-status .status-indicator.offline {
            background-color: red;
        }
        .modal-dialog {
            max-width: 800px;
        }
        .btn-daily-checkin {
            position: absolute;
            right: 15px;
            top: 15px;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.html">6TH UNION</a>
        </nav>
    </header>

    <div class="container mt-5">
        <h2>User Dashboard</h2>
        <div class="online-status">
            <span id="online-status">
                <span class="status-indicator"></span>
                <span id="online-users">Loading...</span>
            </span>
        </div>
        <div class="user-info">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <p><strong>Welcome</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updateProfileModal">Edit Profile</button>
            <a href="logout.php" class="btn btn-secondary mt-2">Logout</a>
            <a href="daily_checkin.php" class="btn btn-success btn-daily-checkin">Daily Check-In</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="update-profile.php" method="post" enctype="multipart/form-data">
                        <!-- Profile Picture Upload -->
                        <div class="form-group">
                            <label for="profile-picture">Profile Picture</label>
                            <input type="file" class="form-control" id="profile-picture" name="profile_picture">
                            <button type="button" class="btn btn-secondary mt-2" id="start-webcam">Start Webcam</button>
                            <button type="button" class="btn btn-primary mt-2" id="capture-photo">Capture Photo</button>
                            <video id="webcam-video" width="320" height="240" autoplay style="display: none;"></video>
                            <canvas id="webcam-canvas" width="320" height="240" style="display: none;"></canvas>
                            <img id="profile-image" src="uploads/default-profile.png" alt="Profile Picture" width="150" style="display: none;">
                        </div>

                        <!-- Other User Information -->
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="profession">Profession</label>
                            <input type="text" class="form-control" id="profession" name="profession" value="<?php echo htmlspecialchars($user['profession']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="skills">Skills</label>
                            <textarea class="form-control" id="skills" name="skills" rows="3"><?php echo htmlspecialchars($user['skills']); ?></textarea>
                        </div>
                        <input type="hidden" id="webcam-image" name="webcam_image">
                        <input type="hidden" name="existing_profile_picture" value="<?php echo htmlspecialchars($user['profile_picture']); ?>">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>Copyright &copy; 2023 6TH UNION Foundation. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        // JavaScript to handle webcam functionality
        document.getElementById('start-webcam').addEventListener('click', function() {
            const video = document.getElementById('webcam-video');
            const canvas = document.getElementById('webcam-canvas');
            const context = canvas.getContext('2d');

            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    video.srcObject = stream;
                    video.style.display = 'block';
                })
                .catch(err => {
                    console.error('Error accessing webcam:', err);
                });
        });

        document.getElementById('capture-photo').addEventListener('click', function() {
            const video = document.getElementById('webcam-video');
            const canvas = document.getElementById('webcam-canvas');
            const context = canvas.getContext('2d');

            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/png');
            document.getElementById('webcam-image').value = imageData;
            document.getElementById('profile-image').src = imageData;
            document.getElementById('profile-image').style.display = 'block';
        });

        // JavaScript to check online status
        function updateOnlineStatus() {
            fetch('count_online_users.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('online-users').textContent = data + ' users online';
                });
        }

        // Update the online status every 30 seconds
        setInterval(updateOnlineStatus, 30000);
        updateOnlineStatus();  // Initial call to set the status
    </script>
</body>
</html>
