<?php
session_start();
require_once 'utils/connect.php';  // Ensure this path is correct

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
    
}

// Fetch the current user's role from the database
$userId = $_SESSION['user_id'];
$query = "SELECT user_role FROM registration WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($userRole);
$stmt->fetch();
$stmt->close();

// Check if the user is a vet clinic
if ($userRole !== 'vetClinic') {
    // Redirect non-vet clinic users to the home or error page
    header("Location: vetclinic-profile.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_name = $_POST['clinic_name'] ?? '';
    $location = $_POST['location'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $description = $_POST['description'] ?? '';
    $services = $_POST['services'] ?? '';
    $open_hours = json_encode($_POST['open_hours'] ?? []);  // Process open hours for each day of the week

    // Handle multiple file uploads for photos
    $photos_paths = [];
    if (!empty($_FILES['photos']['name'][0])) {
        $photos = $_FILES['photos'];
        $upload_dir = 'uploads/vet_clinic_photos/';
        foreach ($photos['tmp_name'] as $key => $tmp_name) {
            $upload_file = $upload_dir . basename($photos['name'][$key]);
            if (move_uploaded_file($tmp_name, $upload_file)) {
                $photos_paths[] = $upload_file;
            }
        }
    }

    // Handle multiple file uploads for videos
    $videos_paths = [];
    if (!empty($_FILES['videos']['name'][0])) {
        $videos = $_FILES['videos'];
        $upload_dir = 'uploads/vet_clinic_videos/';
        foreach ($videos['tmp_name'] as $key => $tmp_name) {
            $upload_file = $upload_dir . basename($videos['name'][$key]);
            if (move_uploaded_file($tmp_name, $upload_file)) {
                $videos_paths[] = $upload_file;
            }
        }
    }

    // Encode paths as JSON
    $photos_paths = json_encode($photos_paths);
    $videos_paths = json_encode($videos_paths);

    // Insert the vet clinic data into the database
    $sql = "INSERT INTO vet_clinics (user_id, clinic_name, location, contact_number, description, services, open_hours, photos, videos)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssssss', $user_id, $clinic_name, $location, $contact_number, $description, $services, $open_hours, $photos_paths, $videos_paths);

    if ($stmt->execute()) {
        // Update user's profile to indicate that they have submitted a vet clinic form
        $update_sql = "UPDATE registration SET has_vet_clinic = TRUE WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('i', $user_id);
        $update_stmt->execute();

        // Save vet clinic data in session to retrieve it later
        $_SESSION['vet_clinic'] = [
            'clinic_name' => $clinic_name,
            'location' => $location,
            'contact_number' => $contact_number,
            'description' => $description,
            'services' => $services,
            'open_hours' => $open_hours,
            'photos' => $photos_paths,
            'videos' => $videos_paths
        ];
    
        // Redirect to profile with a success message
        $_SESSION['success_message'] = "";
        header('Location: profile.php');
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save clinic information']);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join as a Vet Clinic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
         body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom right, #f5f7fa, #c3cfe2);
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 900px;
            margin: 60px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #5d4037;
            font-size: 28px;
            margin-bottom: 20px;
            position: relative;
        }

        h2::after {
            content: '';
            width: 60px;
            height: 4px;
            background: #8d6e63;
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #ddd;
            font-size: 16px;
            transition: all 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #8d6e63;
            box-shadow: 0 4px 10px rgba(141, 110, 99, 0.1);
        }

        textarea {
            resize: vertical;
            height: 120px;
        }

        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #8d6e63, #5d4037);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        button:hover {
            background: linear-gradient(135deg, #5d4037, #3e2723);
        }

        .back-button {
    display: inline-block;
    padding: 12px 20px;
    background: #ff7043; /* Updated to a bright orange tone */
    color: white;
    text-decoration: none;
    font-size: 16px;
    border-radius: 30px; /* More rounded */
    font-weight: bold;
    transition: all 0.3s ease;
    position: absolute;
    top: 30px;
    left: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); /* Added subtle shadow */
}

.back-button:hover {
    background: #ff5722; /* Darker orange on hover */
    color: #f1f1f1; /* Slightly lighter text on hover */
    transform: translateY(-3px); /* Lift effect on hover */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
}


        .open-hours {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .form-group input[type="file"] {
            padding: 10px;
            border: none;
            background: #f1f1f1;
        }

        input[type="file"]:focus {
            border: none;
            box-shadow: none;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

       
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

    </style>
</head>
<body>
<div class="form-container">

<!-- Success/Error Message Display -->
<?php if (isset($_SESSION['success_message'])): ?>
        <div class="message success">
            <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);  // Clear the message after displaying
            ?>
        </div>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="message error">
            <?php 
                echo $_SESSION['error_message']; 
                unset($_SESSION['error_message']);  // Clear the message after displaying
            ?>
        </div>
    <?php endif; ?>

        <div class="back-button">
            <a href="javascript:history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <h2>Join as a Vet Clinic</h2>
        <form action="vetclinic-profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="clinic_name">Clinic Name</label>
                <input type="text" id="clinic_name" name="clinic_name" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="services">Services Offered</label>
                <textarea id="services" name="services" rows="5" required></textarea>
            </div>
            <div class="form-group">
    <label for="open_hours">Opening Hours</label>
    <div class="open-hours">
        <div>
            <label for="general_open">Opening Time</label>
            <select name="open_hours[open]" required>
                <option value="">Select Opening Time</option>
                <option value="08:00 AM">08:00 AM</option>
                <option value="09:00 AM">09:00 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="12:00 PM">12:00 PM</option>
                <option value="01:00 PM">01:00 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
                <option value="05:00 PM">05:00 PM</option>
                <option value="06:00 PM">06:00 PM</option>
            </select>
        </div>
        <div>
            <label for="general_close">Closing Time</label>
            <select name="open_hours[close]" required>
                <option value="">Select Closing Time</option>
                <option value="12:00 PM">12:00 PM</option>
                <option value="01:00 PM">01:00 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
                <option value="05:00 PM">05:00 PM</option>
                <option value="06:00 PM">06:00 PM</option>
                <option value="07:00 PM">07:00 PM</option>
                <option value="08:00 PM">08:00 PM</option>
            </select>
        </div>
    </div>
    <br>

    <div class="form-group">
        <label for="additional_info">Additional Information</label>
        <textarea name="open_hours[additional_info]" id="additional_info" placeholder="e.g., Weekends closed, holiday hours, etc."></textarea>
    </div>
</div>

            <div class="form-group">
                <label for="photos">Photos</label>
                <input type="file" id="photos" name="photos[]" multiple>
            </div>
            <div class="form-group">
                <label for="videos">Videos</label>
                <input type="file" id="videos" name="videos[]" multiple>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>


</body>
</html>
