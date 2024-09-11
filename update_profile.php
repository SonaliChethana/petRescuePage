<?php
session_start();
require_once 'utils/connect.php';  // Include database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

$userId = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch new values from POST data
    $fullName = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $bio = $_POST['bio'];
    $profileImage = null;

    // Handle profile image upload if a file was submitted
    if (!empty($_FILES['profile_image']['name'])) {
        $targetDir = 'uploads/profile-pictures/';
        $fileName = basename($_FILES['profile_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validate the file type (allow only certain formats)
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            // Move the file to the target directory
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                $profileImage = $targetFilePath; // Save the file path for the database
            } else {
                die('There was an error uploading the file.');
            }
        } else {
            die('Only JPG, JPEG, PNG, and GIF files are allowed.');
        }
    }

    // If no new profile image was uploaded, keep the existing image
    if (empty($profileImage)) {
        // Fetch the current profile image from the database to retain it
        $query = "SELECT profile_image FROM profilesettings WHERE profile_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $profileImage = $row['profile_image']; // Retain the existing profile image
    }

    // Update the registration table with new full name, username, and email
    $query = "UPDATE registration SET full_name = ?, username = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $fullName, $username, $email, $userId);
    if (!$stmt->execute()) {
        die('Failed to update registration details.');
    }

    // Update the profilesettings table with new address, bio, and profile image
    $query = "UPDATE profilesettings SET address = ?, bio = ?, profile_image = ?, updated_at = NOW() WHERE profile_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $address, $bio, $profileImage, $userId);
    if (!$stmt->execute()) {
        die('Failed to update profile settings.');
    }

    // Redirect to profile page after successful update
    header("Location: profile.php");
    exit();
}
?>
