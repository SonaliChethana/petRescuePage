<?php
session_start();
require_once 'utils/connect.php';  // Ensure this path is correct

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role']; // Assuming the user role is stored in the session

// Fetch the animal shelter profile from the database
$sql = "SELECT * FROM animal_shelters WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No animal shelter profile found.";
    exit();
}

$shelter = $result->fetch_assoc();

// Decode photos and videos JSON fields
$shelter['photos'] = json_decode($shelter['photos'], true);
$shelter['videos'] = json_decode($shelter['videos'], true);

// Check for JSON errors
if (json_last_error() !== JSON_ERROR_NONE) {
    $shelter['photos'] = [];
    $shelter['videos'] = [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($shelter['shelter_name']); ?> - Vet Clinic Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, #f5f7fa, #c3cfe2);
            margin: 0;
            padding: 0;
        }

        .profile-container {
            max-width: 1000px;
            margin: 60px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }

        .profile-header h1 {
            margin: 0;
            font-size: 32px;
            color: #5d4037;
        }

        .profile-header p {
            margin: 5px 0;
            font-size: 18px;
            color: #555;
        }

        .photos-videos {
            margin-top: 30px;
        }

        .photos-videos h2 {
            font-size: 28px;
            color: #5d4037;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .gallery img {
            width: 100%;
            height: auto;
            max-width: 300px;
            border-radius: 8px;
            object-fit: cover;
        }

        .video-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .video-container video {
            width: 100%;
            max-width: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            font-size: 24px;
            color: #5d4037;
            margin-bottom: 15px;
        }

        .section p {
            font-size: 18px;
            color: #555;
        }

        .back-button {
            display: inline-block;
            padding: 12px 20px;
            background: #ff7043;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease;
            position: absolute;
            top: 30px;
            left: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .back-button:hover {
            background: #ff5722;
            color: #f1f1f1;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <a class="back-button" href="javascript:history.back()">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="profile-container">
        <div class="profile-header">
            <!-- Display Shelter Logo or Profile Picture -->
            <img src="<?php echo htmlspecialchars($shelter['photos'][0] ?? 'default-logo.png'); ?>" alt="Shelter Logo">
            <div>
                <h1><?php echo htmlspecialchars($shelter['shelter_name']); ?></h1>
                <p><?php echo htmlspecialchars($shelter['location']); ?></p>
                <p>Contact: <?php echo htmlspecialchars($shelter['contact_number']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($shelter['description'])); ?></p>
            </div>
        </div>

        <div class="section">
            <h2>Services Offered</h2>
            <p><?php echo nl2br(htmlspecialchars($shelter['services'])); ?></p>
        </div>

        <div class="section">
            <h2>Opening Hours</h2>
            <p>
            <?php
                // Fetch and decode the open_hours JSON field
                $shelter['open_hours'] = json_decode($shelter['open_hours'], true);

                // Check for JSON errors
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $shelter['open_hours'] = [];  // Set to empty array if JSON is invalid
                }

                if (is_array($shelter['open_hours']) && !empty($shelter['open_hours'])) {
                    // Ensure 'open' and 'close' fields exist
                    if (isset($shelter['open_hours']['open']) && isset($shelter['open_hours']['close'])) {
                        echo 'Open: ' . htmlspecialchars($shelter['open_hours']['open']) . '<br>';
                        echo 'Close: ' . htmlspecialchars($shelter['open_hours']['close']) . '<br>';
                    } else {
                        echo "Opening hours data is incomplete.";
                    }
                } else {
                    echo "Opening hours data is not available.";
                }
                ?>
            </p>
        </div>

        <div class="photos-videos">
    <?php if (!empty($shelter['photos']) && is_array($shelter['photos'])): ?>
        <div class="section">
            <h2>Photos</h2>
            <div class="gallery">
                <?php foreach ($shelter['photos'] as $photo): ?>
                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="Shelter Photo">
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="section">
            <h2>Photos</h2>
            <p>No photos available.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($shelter['videos']) && is_array($shelter['videos'])): ?>
        <div class="section">
            <h2>Videos</h2>
            <div class="video-container">
                <?php foreach ($shelter['videos'] as $video): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($video); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="section">
            <h2>Videos</h2>
            <p>No videos available.</p>
        </div>
    <?php endif; ?>
</div>

    </div>
</body>
</html>
