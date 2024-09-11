<?php
session_start();
require_once 'utils/connect.php';  // Ensure this path is correct

if (!isset($_SESSION['user_id'])) {
    // Redirect to login or show error if user is not logged in
    header('Location: Login.php');
    exit();
}

$user_id = $_SESSION['user_id'];


// Fetch user data to check if they have submitted a vet clinic form
$sql = "SELECT user_role, has_vet_clinic, has_animal_shelter FROM registration WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$registration = $result->fetch_assoc();


if (!$registration) {
    die('No registration data found.');
}


// Check if $registration is set and is an array
if (!is_array($registration)) {
    $registration = []; // Initialize as empty array if not set
}



$user_role = $registration['user_role']; // Fetch the role from the user data
$has_vet_clinic = $registration['has_vet_clinic'];
$has_animal_shelter = $registration['has_animal_shelter'];

// Fetch user details from the registration and profilesettings tables
$sql = "SELECT r.full_name, r.username, r.email, ps.address, ps.bio, ps.profile_image
        FROM registration r
        LEFT JOIN profilesettings ps ON r.id = ps.profile_id
        WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}


// Fetch stray dog reports for the logged-in user
$query = "SELECT id, description, location, photos,behaviour,  status, responsible_entity FROM reportstray WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

//fetch emergency reports for the logged-in user
$query = "SELECT id, description, location, photos, status, priority, responsible_entity FROM emergencyreport WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$emergency_reports = [];
while ($row = $result->fetch_assoc()) {
    $emergency_reports[] = $row;
}

//fetch lost dog reports for the logged-in user
$query = "SELECT id, dog_name, dog_age, dog_description, last_seen_location, photos, date, time, status FROM lostandfound WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lost_reports = [];
while ($row = $result->fetch_assoc()) {
    $lost_reports[] = $row;
}



// Fetch stray dog reports for the logged-in user
$query = "SELECT * FROM reportstray WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}


//fetch emergency reports for the logged-in user
$query = "SELECT * FROM emergencyreport WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$emergency_reports = [];
while ($row = $result->fetch_assoc()) {
    $emergency_reports[] = $row;
}

//fetch lost dog reports for the logged-in user
$query = "SELECT * FROM lostandfound WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lost_reports = [];
while ($row = $result->fetch_assoc()) {
    $lost_reports[] = $row;
}


// Handle profile update on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $bio = $_POST['bio'] ?? '';

    // Validate and sanitize input
    $full_name = trim($full_name);
    $username = trim($username);
    $email = trim($email);
    $address = trim($address);
    $bio = trim($bio);

    // Handle file upload for profile image
    $profile_image_path = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $profile_image = $_FILES['profile_image'];
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($profile_image['name']);
        
        if (move_uploaded_file($profile_image['tmp_name'], $upload_file)) {
            $profile_image_path = $upload_file;
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload failed']);
            exit();
        }
    }

    // Update registration table
    $update_registration_sql = "UPDATE registration 
                                SET full_name = ?, username = ?, email = ?
                                WHERE id = ?";
    $update_registration_stmt = $conn->prepare($update_registration_sql);
    $update_registration_stmt->bind_param("sssi", $full_name, $username, $email, $user_id);
    $update_registration_stmt->execute();

    // Update profilesettings table
    if ($profile_image_path) {
        $update_settings_sql = "INSERT INTO profilesettings (profile_id, profile_image, address, bio)
                                VALUES (?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE profile_image = VALUES(profile_image), address = VALUES(address), bio = VALUES(bio)";
        $update_settings_stmt = $conn->prepare($update_settings_sql);
        $update_settings_stmt->bind_param(
            "isss", 
            $user_id, 
            $profile_image_path, 
            $address, 
            $bio
        );
    } else {
        $update_settings_sql = "INSERT INTO profilesettings (profile_id, address, bio)
                                VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE address = VALUES(address), bio = VALUES(bio)";
        $update_settings_stmt = $conn->prepare($update_settings_sql);
        $update_settings_stmt->bind_param(
            "iss", 
            $user_id, 
            $address, 
            $bio
        );
    }
    $update_settings_stmt->execute();

    // Return updated success message
    echo json_encode(['success' => true]);
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
/* General page styling */
body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }

         /* Sidebar Styling */
         .sidebar {
            width: 250px;
            background-color: #6d4c41; /* Dark brown */
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
            position: fixed;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            color: #ffab91; /* Light brown/orange */
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: #fff;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #8d6e63; /* Medium brown */
            color: #fff;
        }

        .sidebar a i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar a span {
            font-size: 16px;
        }


       
/* Style for the home button */
.home-button {
    display: flex;
    align-items: center;
    padding: 20px 20px;
    margin-bottom: 20px; /* Space between home button and menu */
    background: linear-gradient(135deg, #cb6e11 0%, #e7c09a 100%); /* Gradient background */
    color: #333; /* White text color */
    border: 2px solid #ffffff; /* White border */
    border-radius: 8px; /* Rounded corners */
    text-decoration: none;
    font-weight: bold;
    transition: background 0.4s, border-color 0.4s, box-shadow 0.4s; /* Smooth transitions */
    box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Subtle shadow */
}

.home-button:hover {
    background: linear-gradient(135deg, #fcb125 0%, #cb9011 100%); /* Reverse gradient on hover */
    border-color: #e0e0e0; /* Light border color on hover */
    box-shadow: 0 6px 12px rgba(0,0,0,0.3); /* More pronounced shadow */
}

.home-button:active {
    background: linear-gradient(135deg, #8f551a 0%, #9f7e3b 100%); /* Darker gradient when active */
    border-color: #b0b0b0; /* Darker border color when active */
    box-shadow: 0 2px 6px rgba(0,0,0,0.2); /* Reduce shadow when pressed */
}

.home-button i {
    font-size: 28px; /* Larger icon */
    margin-right: 12px; /* More space between icon and text */
    margin-left: 50px; /* More space between icon and left edge */
}

.home-button span {
    font-size: 20px; /* Larger text */
    font-family: 'Arial', sans-serif; /* Change font-family if needed */
    font-weight: 800; /* Slightly bolder text */
}


        /* Main content styling */
        .main-content {
            margin-left: 600px;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
        }

         /* Profile form styling */
         .content {
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            max-width: 500px;
            width: 100%;
            position: relative;
        }

        h2 {
            text-align: center;
            color: #6d4c41; /* Dark brown */
            margin-bottom: 20px;
            font-size: 28px;
        }

        /* Profile Image Styling */
        .profile-picture {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-picture img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6d4c41; /* Dark brown border */
        }

        .upload-btn {
            margin-top: 10px;
            background-color: #8d6e63; /* Medium brown */
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .upload-btn:hover {
            background-color: #6d4c41; /* Dark brown */
        }

        .upload-btn input[type="file"] {
            display: none;
        }
    
                       /* Button Styles */
                       #changeProfilePictureBtn {
            display: inline-flex;
            align-items: center;
            background-color: #e1a050;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            border: 2px solid #4d3703;
            position: relative;
            overflow: hidden;
        }

        #changeProfilePictureBtn::before {
            content: '📷'; /* Add an icon or image before the text */
            font-size: 1.25rem;
            margin-right: 0.5rem;
        }

        #changeProfilePictureBtn input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        #changeProfilePictureBtn:hover {
            background-color: #b36e00;
            transform: translateY(-2px);
        }

        #changeProfilePictureBtn:active {
            background-color: #944f00;
            transform: translateY(0);
        }
        /* Form styling */
        .edit-form {
            display: flex;
            flex-direction: column;
        }

        .edit-form label {
            font-weight: bold;
            margin-top: 15px;
            color: #6d4c41; /* Dark brown */
        }

        .edit-form input,
        .edit-form textarea {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #8d6e63; /* Medium brown */
            border-radius: 5px;
            font-size: 14px;
            color: #333;
            background-color: #f9f4f1; /* Light beige */
        }

        .edit-form input[readonly] {
            background-color: #e9e9e9;
            cursor: not-allowed;
        }

        .edit-form textarea {
            resize: none;
            min-height: 100px;
        }

        /* Button styling */
        .edit-form button {
            padding: 10px;
            background-color: #6d4c41; /* Dark brown */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .edit-form button:hover {
            background-color: #8d6e63; /* Medium brown */
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="Dashboard.php" class="home-button"><i>🏠</i><span>Home</span></a>

    <h2>Profile Menu</h2>
    <a href="profile.php"><i>👤</i><span>Profile Settings</span></a>
    <a href="stray_dog_reports.php"><i>🐕</i><span>Your Stray Dog Reports</span></a>
    <a href="emergency_reports.php"><i>🚨</i><span>Your Emergency Reports</span></a>
    <a href="lost_dog_reports.php"><i>🔍</i><span>Your Lost Dog Reports</span></a>
            
        <?php
        // Check if the user has registered as a vet clinic to display the "Add your Vet Clinic" link
        if (isset($registration['has_vet_clinic']) && $registration['has_vet_clinic']): ?>
            <a href="vetclinic-profile.php"><i>🏥</i><span>Add your Vet Clinic</span></a>
        <?php endif; ?>

        <?php
        // Check if 'has_vet_clinic' is true, to display "Vet Clinic Profile"
        if (isset($registration['has_vet_clinic']) && $registration['has_vet_clinic']): ?>
            <div class="menu-item">
                <a href="vetclinic_profile_display.php">Vet Clinic Profile</a>
            </div>
        <?php endif; ?>


        <?php
        // Check if the user has registered as an animal shelter to display the "Add your Animal Shelter" link
        if (isset($registration['has_animal_shelter']) && $registration['has_animal_shelter']): ?>
            <a href="animalshelter-profile.php"><i>🏠</i><span>Add your Animal Shelter</span></a
        <?php endif; ?>

        <?php
        // Check if 'has_animal_shelter' is true, to display "Animal Shelter Profile"
        if (isset($registration['has_animal_shelter']) && $registration['has_animal_shelter']): ?>
            <div class="menu-item">
                <a href="animalshelter_profile_display.php">Animal Shelter Profile</a>
            </div>
        <?php endif; ?>


</div>

    <div class="main-content">

        <div class="content">
            <h2>User Profile</h2>
            <form id="profileForm" class="edit-form" enctype="multipart/form-data">

                    <div class="profile-picture">
                        <img id="profileImagePreview" src="<?php echo $user['profile_image'] ?: 'C:\xampp\htdocs\petRescue\images\default-profile.jpg'; ?>" alt="Profile Picture">
                        <label class="upload-btn" id="changeProfilePictureBtn">
                            Change Profile Picture
                            <input type="file" name="profile_image" id="profile_image">
                        </label>
                    </div>

                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">

                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" >

                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>">

                    <label for="address">Address:</label>
                    <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user['address']); ?>">

                    <label for="bio">Bio:</label>
                    <textarea name="bio" id="bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>

                    <button type="submit">Save Changes</button>
                </form>
            </div>
        </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#profile_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#profileImagePreview').attr('src', event.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#profileForm').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'profile.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            alert('Profile updated successfully');
                        } else {
                            alert(data.message || 'Failed to update profile');
                        }
                    },
                    error: function() {
                        alert('An error occurred while updating the profile');
                    }
                });
            });
        });
    </script>
</body>
</html>
