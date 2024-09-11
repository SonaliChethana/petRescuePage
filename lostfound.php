<?php
session_start();
require_once 'utils/connect.php'; // Adjust this to your actual connection file


// Check if the user is logged in and retrieve user_id from session
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];


// Check if the form was submitted with POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['update_status'])) {
        // Handle status update
        $reportId = $_POST['report_id'];
        $status = $_POST['status'];

        // Update the status in the database
        $stmt = $conn->prepare("UPDATE lostandfound SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $reportId);

        if ($stmt->execute()) {
            $response = array("success" => true, "message" => "Status updated successfully!");
        } else {
            $response = array("success" => false, "message" => "Error updating status: " . $stmt->error);
        }

        $stmt->close();
        echo json_encode($response);
        $conn->close();
        exit();
    }



    
    

    $date = $_POST['date']; // e.g., '2024-08-24'
    $time = $_POST['time']; // e.g., '14:30'
    
    // Combine them into a single datetime string
    $datetime = $date . ' ' . $time; // e.g., '2024-08-24 14:30'

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // File upload handling
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["dogPhoto"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["dogPhoto"]["tmp_name"]);
    if ($check === false) {
        $response = array("success" => false, "message" => "File is not an image.");
        echo json_encode($response);
        exit();
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['photo']['name']);
    
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $photoPath = $uploadFile; // Save this path to the database
        }
    }
    

    // Check file size
    if ($_FILES["dogPhoto"]["size"] > 2 * 1024 * 1024) {
        $response = array("success" => false, "message" => "Sorry, your file is too large.");
        echo json_encode($response);
        exit();
    }

    // Allow only certain file formats
    $allowedTypes = array('jpg', 'jpeg', 'png');
    if (!in_array($imageFileType, $allowedTypes)) {
        $response = array("success" => false, "message" => "Only JPG, JPEG, PNG files are allowed.");
        echo json_encode($response);
        exit();
    }

    
    // Move uploaded file
    if (move_uploaded_file($_FILES["dogPhoto"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, now insert details into database
        $dogName = $_POST['name'];
        $dogAge = $_POST['age'];
        $dogDescription = $_POST['description'];
        $lastSeenLocation = $_POST['location'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $photoPath = $targetFile;

        

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO lostandfound (dog_name, dog_age, dog_description, photos,last_seen_location, date, time,  user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssis", $dogName, $dogAge, $dogDescription, $photoPath, $lastSeenLocation, $date, $time,  $user_id);

    



        if ($stmt->execute()) {
                $response = array("success" => true);
                echo json_encode($response);
            } else {
                $response = array("success" => false, "message" => "Error saving dog details to database: " . $stmt->error);
                echo json_encode($response);
            }

            $stmt->close();



    } else {
        $response = array("success" => false, "message" => "Sorry, there was an error uploading your file.");
        echo json_encode($response);
    }

    
    


    $conn->close();
    exit();


}


   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reunite Paws - Report a Lost Dog</title>
    <link rel="stylesheet" href="css/lostfound.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <style>


 /* Header */
 .header {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('images/bg-1.jpg') no-repeat center center/cover; /* Gradient overlay with background image */
            color: #fff; /* Text color for readability */
    padding: 20px 20px; /* Padding around the header content */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
    position: relative; /* Position relative for overlay effect */
    width: 100%;
    z-index: 2; /* Ensure it is above the hero content */
    display: flex;
    justify-content: space-between; /* Space out logo and navigation */
    align-items: center; /* Center-align header items vertically */
    animation: fadeInDown 1s ease-out; /* Animation for header */
}

.header-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap; /* Wrap items on smaller screens */
    width: 100%;

}

.logo {
    display: flex;
    align-items: center;
    font-family: 'Poppins', sans-serif;
    font-size: 1.2em; /* Adjusted font size */
    color: #ff7043; /* Matching warm color */
    text-transform: uppercase; /* Capitalize for emphasis */
    margin-right: 40px; /* Reduced space between logo and title */

}

.logo img {
    max-width: 100px; /* Adjusted logo size */
    margin-right: 15px; /* Space between logo image and text */
    border-radius: 50%; /* Circular shape for a friendly look */
    border: 3px; /* Border around the logo */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Enhanced shadow for depth */
    height: 100px;
}

.title {
    font-family: 'Poppins', sans-serif;
    font-size: 2.5em; /* Larger font size for emphasis */
    color: #f4be77; /* Warm color */
    text-transform: uppercase; /* Capitalize for emphasis */
    display: flex;
    align-items: center;
    margin-right: 100px; /* Increased space between title and nav */

}

.title::before {
    content: '🐾'; /* Paw icon before the title */
    font-size: 1.2em; /* Larger icon */
    margin-right: 10px;
    color: #ff7043;
}

.title::after {
    content: '🐕'; /* Dog icon after the title */
    font-size: 1.2em; /* Larger icon */
    margin-left: 10px;
    color: #ff7043;
    transform: rotate(10deg);
}

.title:hover::before, .title:hover::after {
    transform: scale(1.2) rotate(-10deg); /* Slight tilt and scale on hover */
    transition: transform 0.3s ease;
}

.title:hover {
    color: #d9534f; /* Slight color change on hover for interactivity */
}

.nav {
    display: flex;
    align-items: center; /* Center-align navigation items vertically */
    margin-left: auto; /* Push navigation to the right */
    margin-right: 5px; /* Additional margin to push navigation further right */


}

.nav-list {
    display: flex;
    justify-content: flex-end; /* Push navigation buttons to the right */
    list-style: none;
    margin: 0;
    padding: 0;
    margin-right: 5px; /* Adjust this value to push the menu further right */

    
}

.nav-item {
    margin-left: 10px; /* Space between nav items */
}

.home-button {
    margin-right: 1px; /* Adjust this value to push the Home button further right */
}


.nav-link {
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    padding: 10px 20px;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 25px;
    transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.nav-link:active {
    transform: translateY(1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}


/* Responsive Styles for Header */
@media (max-width: 1200px) {
    .header {
        padding: 15px; /* Adjust padding for larger tablets and smaller desktops */
    }

    .logo img {
        max-width: 80px; /* Reduce logo size for smaller screens */
        height: 80px; /* Adjust height for smaller screens */
    }

    .title {
        font-size: 2em; /* Reduce font size for title */
        margin-right: 50px; /* Reduce margin for smaller screens */
    }

    .title::before,
    .title::after {
        font-size: 1em; /* Adjust icon size */
    }

    .nav {
        margin-right: 10px; /* Reduce margin for navigation */
    }

    .nav-list {
        margin-right: 10px; /* Adjust margin for list */
    }
}

@media (max-width: 992px) {
    .header {
        flex-direction: column; /* Stack logo and title vertically */
        align-items: flex-start; /* Align items to the start */
        padding: 15px; /* Adjust padding */
    }

    .logo {
        margin-bottom: 10px; /* Space below logo */
    }

    .title {
        font-size: 1.8em; /* Further reduce font size for title */
        margin-right: 0; /* Remove margin for better alignment */
    }

    .nav {
        margin: 10px 0; /* Space above and below navigation */
        width: 100%; /* Full width for navigation */
        justify-content: flex-start; /* Align items to the start */
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        width: 100%; /* Full width for navigation list */
    }

    .nav-item {
        margin-left: 0; /* Remove left margin for vertical layout */
        margin-bottom: 10px; /* Space between navigation items */
    }
}

@media (max-width: 768px) {
    .header {
        padding: 10px; /* Adjust padding for smaller tablets and larger phones */
    }

    .logo img {
        max-width: 60px; /* Further reduce logo size */
        height: 60px; /* Adjust height */
    }

    .title {
        font-size: 1.6em; /* Further reduce font size for title */
    }

    .title::before,
    .title::after {
        font-size: 0.9em; /* Adjust icon size */
    }

    .nav {
        margin: 10px 0; /* Adjust margin */
        width: 100%; /* Full width for navigation */
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        width: 100%; /* Full width for navigation list */
    }

    .nav-item {
        margin-left: 0; /* Remove left margin */
        margin-bottom: 10px; /* Space between items */
    }

    .nav-link {
        padding: 8px 16px; /* Adjust padding for navigation links */
    }
}

@media (max-width: 576px) {
    .header {
        padding: 10px 5px; /* Reduce padding for small screens */
    }

    .logo img {
        max-width: 50px; /* Further reduce logo size */
        height: 50px; /* Adjust height */
    }

    .title {
        font-size: 1.4em; /* Further reduce font size */
    }

    .title::before,
    .title::after {
        font-size: 0.8em; /* Adjust icon size */
    }

    .nav {
        width: 100%; /* Full width for navigation */
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        width: 100%; /* Full width for navigation list */
    }

    .nav-item {
        margin-left: 0; /* Remove left margin */
        margin-bottom: 8px; /* Space between items */
    }

    .nav-link {
        padding: 6px 12px; /* Adjust padding for small screens */
    }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
         
        .footer {
            background: linear-gradient(135deg, #6f4501, #9e571d);
            color: #ecf0f1;
            padding: 40px 0;
            font-family: 'Arial', sans-serif;
        }
        
        /* Footer Content */
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        
        /* Footer Sections */
        .footer-about, .footer-contact, .footer-subscribe, .footer-social {
            flex: 1;
            margin: 10px;
            max-width: 25%;
        }
        
        .footer-about h3, .footer-contact h3, .footer-subscribe h3, .footer-social h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
            font-weight: bold;
        }
        
        .footer-about p, .footer-contact p, .footer-subscribe p {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        /* Footer Subscribe Form */
        .footer-subscribe form {
            display: flex;
            flex-direction: column;
        }
        
        .footer-subscribe input[type=email] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        
        .btn-subscribe {
            background: #db8534;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        
        .btn-subscribe:hover {
            background: #b98729;
        }
        
        /* Social Icons */
        .footer-social a {
            color: #ecf0f1;
            font-size: 1.5rem;
            margin-right: 10px;
            transition: color 0.3s ease;
        }
        
        .footer-social a:hover {
            color: #dba134;
        }
        
        /* Footer Bottom */
        .footer-bottom {
            text-align: center;
            padding: 10px;
            background: #50402c;
            margin-top: 20px;
            border-top: 1px solid #5e3b34;
        }
        
        .footer-bottom p {
            margin: 0;
        }
        
        /* Responsive Styles */
        @media (max-width: 767px) {
            .footer-content {
                flex-direction: column;
                align-items: center;
            }
        }
        
        
        /* Responsive Styles */
        @media (max-width: 767px) {
            .footer-content {
                flex-direction: column;
                align-items: center;
            }
        
            .footer-card {
                margin-bottom: 20px;
            }
        }
        
        
        /* Responsive Styles */
        @media (max-width: 767px) {
            .footer-content {
                flex-direction: column;
                align-items: center;
            }
        
            .footer-content > div {
                margin-bottom: 20px;
            }
        }

       /* Report Section */
.report-section {
    background: linear-gradient(135deg, #ff9a9e, #fad0c4); /* Gradient background */
    border-radius: 15px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    padding: 30px; /* Padding around the content */
    margin: 20px auto; /* Center the section with margin */
    max-width: 800px; /* Limit the width for better readability */
    position: relative; /* Position for pseudo-elements */
    overflow: hidden; /* Hide overflow to maintain rounded corners */
}

/* Header Styles */
.report-section-header {
    text-align: center; /* Center-align text */
    margin-bottom: 20px; /* Space below the header */
}

.report-title {
    font-family: 'Poppins', sans-serif;
    font-size: 2em; /* Larger font size for the title */
    color: #333; /* Dark color for readability */
    margin-bottom: 10px; /* Space below the title */
    position: relative; /* Position for pseudo-elements */
    display: inline-block;
}

.report-title::before, .report-title::after {
    content: '📝'; /* This will be overridden by the specific rule for ::after */
    font-size: 1.5em;
    color: #ff7043;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
}

.report-title::before {
    content: '📝'; /* Icon before the title */
    left: -60px;
}

.report-title::after {
    content: '🐕'; /* Icon after the title */
    right: -60px;
}


.report-description {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1em; /* Slightly smaller font size */
    color: #666; /* Lighter color for description */
    margin-top: 0; /* Remove margin on top */
}


/* Responsive Styles */
@media (max-width: 768px) {
    .report-section {
        padding: 20px;
        margin: 15px auto;
        max-width: 100%;
    }

    .report-title {
        font-size: 1.8em;
    }

    .report-title::before {
        left: -40px; /* Adjust positioning for smaller screens */
        font-size: 1.3em; /* Adjust font size for smaller screens */
    }

    .report-title::after {
        right: -40px; /* Adjust positioning for smaller screens */
        font-size: 1.3em; /* Adjust font size for smaller screens */
    }
}
    .report-description {
        font-size: 1em;
    }


@media (max-width: 480px) {
    .report-section {
        padding: 15px;
        margin: 10px auto;
    }

    .report-title {
        font-size: 1.5em;
    }
    .report-title::before {
        left: -20px; /* Further adjust for very small screens */
        font-size: 1.2em; /* Adjust font size for very small screens */
    }

    .report-title::after {
        right: -20px; /* Further adjust for very small screens */
        font-size: 1.2em; /* Adjust font size for very small screens */
    }


    .report-description {
        font-size: 0.9em;
    }
}


/* General form section styling */
.form-section {
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    background: linear-gradient(135deg, #e0f7fa, #b2ebf2); /* Gradient background */
    border-radius: 12px; /* Rounded corners */
    padding: 40px; /* Increased padding for spacious look */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25); /* Deeper shadow for more depth */
    max-width: 700px; /* Set maximum width */
    margin: auto; /* Center container horizontally */
    position: relative; /* For positioning absolute elements */
    border: 1px solid #e0e0e0; /* Light border for added definition */
    font-family: 'Roboto', sans-serif; /* Modern font */
}


/* Form fields container */
.form-fields {
    padding: 30px;
    border-radius: 10px; /* Rounded corners for the form container */
    background: #ffffff; /* White background for fields */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Enhanced shadow */
    border: 1px solid #ddd; /* Light border for fields */
    width: 600px;
}

/* Form groups */
.form-group {
    margin-bottom: 25px; /* More space between fields */
}

.form-group label {
    display: block;
    font-size: 1.25rem; /* Larger font size */
    color: #333; /* Darker color for readability */
    margin-bottom: 10px; /* More space between label and input */
    font-weight: 600; /* Bold labels for emphasis */
}

/* Inputs styling */
.form-group input[type="text"],
.form-group input[type="file"] {
    width: 100%;
    padding: 14px;
    border: 1px solid #ccc; /* Light border */
    border-radius: 10px; /* Rounded corners */
    font-size: 1.1rem; /* Slightly larger font size */
    background-color: #fafafa; /* Light background */
    transition: all 0.3s ease; /* Smooth transition for focus */
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle inner shadow */
}

.form-group input[type="text"]:focus,
.form-group input[type="file"]:focus {
    border-color: #007bff; /* Highlight border color on focus */
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.3), inset 0 2px 4px rgba(0, 0, 0, 0.1); /* Soft shadow and focus effect */
    outline: none;
}

/* Small text styling */
.form-group small {
    display: block;
    color: #666; /* Lighter color for help text */
    font-size: 0.95rem; /* Slightly smaller font size */
    margin-top: 5px; /* Space above help text */
}

/* Button styling */
.form-buttons {
    display: flex;
    justify-content: space-between; /* Space out buttons */
    margin-top: 30px; /* Space above buttons */
}

.form-buttons button {
    padding: 14px 28px; /* Larger padding for buttons */
    border: none;
    border-radius: 10px; /* Rounded corners */
    font-size: 1.1rem; /* Slightly larger font size */
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease; /* Smooth transitions */
    font-weight: 500; /* Slightly bolder text */
}

.cancel-button {
    background: linear-gradient(135deg, #f44336, #d32f2f); /* Gradient background for cancel */
    color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
}

.cancel-button:hover {
    background: linear-gradient(135deg, #d32f2f, #b71c1c); /* Darker gradient on hover */
    transform: scale(1.05); /* Slight zoom effect */
}

.submit-button {
    background: linear-gradient(135deg, #4caf50, #388e3c); /* Gradient background for submit */
    color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
}

.submit-button:hover {
    background: linear-gradient(135deg, #388e3c, #2c6b2f); /* Darker gradient on hover */
    transform: scale(1.05); /* Slight zoom effect */
}


#map {
            height: 400px;
            width: 100%;
        }




        .use-location-button {
    background-color: #4CAF50; /* Green background color */
    border: none; /* Remove border */
    color: white; /* White text color */
    padding: 10px 20px; /* Add some padding */
    text-align: center; /* Center text */
    text-decoration: none; /* Remove underline */
    display: inline-block; /* Align with other elements */
    font-size: 16px; /* Adjust font size */
    margin: 10px 0; /* Add margin for spacing */
    cursor: pointer; /* Pointer cursor on hover */
    border-radius: 5px; /* Rounded corners */
    transition: background-color 0.3s, transform 0.3s; /* Smooth transitions */
}

.use-location-button:hover {
    background-color: #45a049; /* Darker green on hover */
    transform: scale(1.05); /* Slightly enlarge on hover */
}

.use-location-button:focus {
    outline: none; /* Remove default focus outline */
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow on focus */
}

/* Responsive Styles for Form Section */
@media (max-width: 1024px) {
    .form-section {
        padding: 30px; /* Adjust padding for medium screens */
        max-width: 90%; /* Make form section take up more width on medium screens */
    }

    .form-fields {
        width: 100%; /* Make the form fields container take up full width */
    }
}

@media (max-width: 768px) {
    .form-section {
        padding: 20px; /* Adjust padding for smaller screens */
    }

    .form-fields {
        width: 100%; /* Ensure form fields container takes full width */
    }

    .form-group {
        margin-bottom: 20px; /* Reduce space between fields */
    }

    .form-group label {
        font-size: 1rem; /* Smaller font size for labels */
    }

    .form-group input[type="text"],
    .form-group input[type="file"] {
        font-size: 1rem; /* Smaller font size for inputs */
        padding: 12px; /* Adjust padding for smaller inputs */
    }

    .form-buttons {
        flex-direction: column; /* Stack buttons vertically */
        align-items: stretch; /* Make buttons take full width */
    }

    .form-buttons button {
        width: 100%; /* Make buttons take full width */
        margin-bottom: 10px; /* Space between buttons */
    }

    .use-location-button {
        font-size: 14px; /* Adjust font size for smaller screens */
        padding: 8px 16px; /* Adjust padding for smaller screens */
    }
}

@media (max-width: 480px) {
    .form-section {
        padding: 15px; /* Adjust padding for very small screens */
    }

    .form-fields {
        width: 100%; /* Ensure full width for the form fields container */
    }

    .form-group {
        margin-bottom: 15px; /* Reduce space between fields */
    }

    .form-group label {
        font-size: 0.9rem; /* Smaller font size for labels */
    }

    .form-group input[type="text"],
    .form-group input[type="file"] {
        font-size: 0.9rem; /* Smaller font size for inputs */
        padding: 10px; /* Adjust padding for smaller inputs */
    }

    .form-buttons button {
        padding: 12px; /* Adjust padding for buttons */
        font-size: 1rem; /* Smaller font size for buttons */
    }

    .use-location-button {
        font-size: 12px; /* Smaller font size for button */
        padding: 6px 12px; /* Adjust padding for button */
    }
}


/* Base style for popup messages */
.popup-message {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(0, 0, 0, 0.8);
    color: #fff;
    padding: 20px;
    border-radius: 10px;
    z-index: 1000;
    display: none;
    text-align: center;
    font-size: 1.2em;
}

/* Success message style */
.popup-message.success {
    background-color: #4CAF50; /* Green background for success */
}

/* Error message style */
.popup-message.error {
    background-color: #f44336; /* Red background for error */
}


.reports-section {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.reports-section h2 {
    text-align: center;
    color: #333;
    font-size: 28px;
    margin-bottom: 20px;
    font-weight: 600;
    position: relative;
}

.report {
    display: flex;
    align-items: center;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 20px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    padding: 15px;
}

.report:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Modal styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed; 
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.8); /* Black background with opacity */
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 80%;
    max-height: 80%;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.modal-content:focus {
    outline: none;
}

.close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* Image styles */
.report img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    margin-right: 20px;
    border: 4px solid #ffc107;
    cursor: pointer;
    transition: border 0.3s ease, transform 0.3s ease;
}

.report img:hover {
    transform: scale(1.05);
}

.report-details {
    flex: 1;
    flex-direction: column;
}

.report-details h3 {
    font-size: 22px;
    margin: 0;
    color: #343a40;
    font-weight: 600;
}

.report-details p {
    margin: 5px 0;
    color: #555;
    font-size: 16px;
    line-height: 1.4;
}

.report-details p:last-child {
    color: #888;
}

.report-details h3::after {
    content: '';
    display: block;
    width: 50px;
    height: 2px;
    background-color: #ffc107;
    margin-top: 5px;
}



.report-status-container {
    display: flex;
    align-items: center;
    gap: 10px; /* Space between dropdown and button */
}

.report-status-container select {
    padding: 8px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
}

.report-status-container button {
    padding: 8px 16px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.report-status-container button:hover {
    background-color: #0056b3;
}

/* Responsive Styles for Reports Section */
@media (max-width: 1024px) {
    .reports-section {
        padding: 15px; /* Reduce padding for medium screens */
    }

    .report {
        flex-direction: column; /* Stack content vertically */
        align-items: flex-start; /* Align items to the start */
        padding: 10px; /* Reduce padding inside report */
    }

    .report img {
        width: 120px; /* Smaller image size for medium screens */
        height: 120px; /* Smaller image size for medium screens */
        margin-right: 0; /* Remove margin on smaller screens */
        margin-bottom: 10px; /* Add margin below image */
    }

    .modal-content {
        max-width: 90%; /* Adjust modal content width */
        max-height: 90%; /* Adjust modal content height */
    }
}

@media (max-width: 768px) {
    .reports-section {
        padding: 10px; /* Further reduce padding */
    }

    .report {
        flex-direction: column; /* Ensure vertical stacking */
        align-items: center; /* Center-align items */
        padding: 5px; /* Further reduce padding inside report */
    }

    .report img {
        width: 100px; /* Smaller image size for small screens */
        height: 100px; /* Smaller image size for small screens */
        margin-bottom: 10px; /* Add margin below image */
    }

    .report-details h3 {
        font-size: 20px; /* Reduce font size for headings */
    }

    .report-details p {
        font-size: 14px; /* Reduce font size for paragraphs */
    }

    .modal-content {
        max-width: 95%; /* Further adjust modal content width */
        max-height: 95%; /* Further adjust modal content height */
    }
}

@media (max-width: 480px) {
    .reports-section {
        padding: 5px; /* Minimal padding for very small screens */
    }

    .report {
        flex-direction: column; /* Ensure vertical stacking */
        align-items: center; /* Center-align items */
        padding: 5px; /* Minimal padding inside report */
    }

    .report img {
        width: 80px; /* Smaller image size for very small screens */
        height: 80px; /* Smaller image size for very small screens */
    }

    .report-details h3 {
        font-size: 18px; /* Further reduce font size for headings */
    }

    .report-details p {
        font-size: 12px; /* Further reduce font size for paragraphs */
    }

    .modal-content {
        max-width: 100%; /* Full width for modal content */
        max-height: 100%; /* Full height for modal content */
    }
}



</style>
</head>
<body>
<header class="header">
        <div class="header-container">
            <div class="logo">
                <img src="images/logo.png" alt="StraySaver Logo">
                <h1 class="title">StraySaver</h1>
            </div>
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="Dashboard.php" class="nav-link home-button">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="report-section">
            <div class="report-section-header">
                <h2 class="report-title">Report a Lost Dog</h2>
                <p class="report-description">Fill out the form below to report a lost dog.</p>
            </div>
    
        </div>

        <div class="form-section">
        
            <form method="POST" action="lostfound.php" id="lostDogForm" enctype="multipart/form-data">
            
                <!-- Success message container -->
                 <div class="form-fields">
                <div id="successMessageContainer"></div>
                <div class="form-group">
                    <label for="dogPhoto">Upload Photo</label>
                    <input type="file" id="dogPhoto" name="dogPhoto" accept="image/png, image/jpeg" required>
                    <small>Accepted formats: PNG & JPEG. Max file size: 2MB.</small>
                </div>
                <div class="form-group">
                    <label for="name">Name of the Dog</label>
                    <input type="text" id="name" name="name" placeholder="Enter name" required>
                    <small>Please provide the dog's name.</small>
                </div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="text" id="age" name="age" placeholder="Enter age" required>
                    <small>Estimated age in years.</small>
                </div>
                <div class="form-group">
                    <label for="description">Physical Description</label>
                    <input type="text" id="description" name="description" placeholder="Enter description" required>
                    <small>e.g. breed, color, size.</small>
                </div>
                <div class="form-group">
                    <label for="location">Last Seen Location</label>
                    <input type="text" id="location" name="location" placeholder="Enter location" required>
                    <button type="button" class="use-location-button" onclick="getLocation()">Use My Current Location</button>

                    <small>Where the dog was last seen.</small>
                    <div id="map"></div>

                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>
                    <small>Select the date when the dog was last seen.</small>
                </div>

                <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time" required>
                    <small>Select the time when the dog was last seen.</small>
                </div>



                <div class="form-buttons">
                    <button type="reset" class="cancel-button">Cancel</button>
                    <button type="submit" class="submit-button">Submit</button>
                </div>
            </div>
            <div id="popupMessageContainer"></div>

            </form>
        </div>
        <div id="popupMessageContainer" class="popup-message"></div>


        <div class="reports-section">
            <h2>Lost Dog Reports</h2>
            <?php
        require_once 'utils/connect.php'; // Adjust this to your actual connection file

        // Fetch data from database
        $sql = "SELECT id, dog_name, dog_age, dog_description, last_seen_location, date, time, photos, status FROM lostandfound";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reportId = htmlspecialchars($row['id']);
                $dogName = htmlspecialchars($row['dog_name']);
                $dogAge = htmlspecialchars($row['dog_age']);
                $dogDescription = htmlspecialchars($row['dog_description']);
                $lastSeenLocation = htmlspecialchars($row['last_seen_location']);
                $date = htmlspecialchars($row['date']);
                $time = htmlspecialchars($row['time']);
                $photoPath = htmlspecialchars($row['photos']);
                $status = htmlspecialchars($row['status']);

                // Output each report as HTML
                echo '<div class="report">';
                echo '<img src="' . $photoPath . '" alt="' . $dogName . '">';
                echo '<div class="report-details">';
                echo '<h3>Name: ' . $dogName . '</h3>';
                echo '<p>Age: ' . $dogAge . ' years</p>';
                echo '<p>Description: ' . $dogDescription . '</p>';
                echo '<p>Last Seen: ' . $lastSeenLocation . '</p>';
                echo '<p>Date: ' . $date . '</p>';
                echo '<p>Time: ' . $time . '</p>';
                echo '<div class="report-status-container">';
                echo '<p>Status: <select class="status-dropdown" data-report-id="' . $reportId . '">';
            echo '<option value="missing"' . ($status == 'missing' ? ' selected' : '') . '>Missing</option>';
            echo '<option value="found"' . ($status == 'found' ? ' selected' : '') . '>Found</option>';
            echo '</select></p>';
            echo '<button class="save-status-button" data-report-id="' . $reportId . '">Save Status</button>';
            echo '</div>';
            


                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No lost dog reports found.</p>';
        }

        // Close the database connection
        $conn->close();
        ?>




        </div>

        <!-- Modal structure -->
        <div id="imageModal" class="modal">
            <span class="close">&times;</span>
            <img class="modal-content" id="modalImage">
        </div>

    </main>
    <footer id="footer-section" class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-about">
                <h3>About Us</h3>
                <p>StraySaver is committed to rescuing, rehabilitating, and finding new homes for stray pets. Together, we make a difference in their lives.</p>
            </div>
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p>Email: <a href="mailto:straysaver10@gmail.com">straysaver10@gmail.com</a></p>
                <p>Phone: <a href="tel:+1234567890">+1 234 567 890</a></p>
                <p>Address: No.6/ Dickmens Road, Colombo 6</p>
            </div>
            <div class="footer-subscribe">
                <h3>Subscribe</h3>
                <p>Stay updated with our latest news and events. Sign up for our newsletter.</p>
                <form action="subscribe.php" method="post">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn-subscribe">Subscribe</button>
                </form>
            </div>
            <div class="footer-social">
                <h3>Follow Us</h3>
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 StraySaver. All rights reserved.</p>
        </div>
    </div>
</footer>


<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


    <script>
        // Initialize Pikaday datepicker
        new Pikaday({
            field: document.getElementById('datetime'),
            format: 'YYYY-MM-DD HH:mm:ss',
            onSelect: function() {
                document.getElementById('datetime').value = this.getMoment().format('YYYY-MM-DD HH:mm:ss');
            }
        });


        function showPopupMessage(message, isSuccess) {
    var popupMessageContainer = document.getElementById('popupMessageContainer');
    popupMessageContainer.textContent = message;
    popupMessageContainer.className = 'popup-message ' + (isSuccess ? 'success' : 'error');
    popupMessageContainer.style.display = 'block';

    setTimeout(function() {
        popupMessageContainer.style.display = 'none';
    }, 2000);
}




document.getElementById('lostDogForm').addEventListener('submit', function(event) {
    event.preventDefault();
    var formData = new FormData(this);

    fetch('lostfound.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showPopupMessage('Dog details saved successfully!', true);
            document.getElementById('lostDogForm').reset(); // Clear the form
        } else {
            showPopupMessage(data.message, false);
        }
    })
    .catch(error => {
        showPopupMessage('An error occurred. Please try again.', false);
        console.error('Error:', error);
    });
});


        


        document.getElementById('lostDogForm').addEventListener('submit', function(event) {
        const date = document.getElementById('date').value;
        const time = document.getElementById('time').value;
        const datetime = date + ' ' + time;

        // Create a hidden input to store the combined datetime value
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'datetime';
        hiddenInput.value = datetime;
        this.appendChild(hiddenInput);
    });


    var map;
var marker;

function initMap(lat = 6.9271, lon = 79.8612) {
    map = L.map('map').setView([lat, lon], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    marker = L.marker([lat, lon], {draggable: true}).addTo(map);
    marker.on('moveend', function(e) {
        var latLng = e.target.getLatLng();
        reverseGeocode(latLng.lat, latLng.lng);
    });

    // Add click event listener to the map
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lon = e.latlng.lng;
        marker.setLatLng([lat, lon]);
        map.setView([lat, lon], 15);
        reverseGeocode(lat, lon);
    });
}

function reverseGeocode(lat, lon) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            var locationName = data.display_name;
            document.getElementById("location").value = locationName;
        })
        .catch(error => console.error('Error:', error));
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    var lat = position.coords.latitude;
    var lon = position.coords.longitude;
    updateLocationFields(lat, lon);
    map.setView([lat, lon], 15);
    marker.setLatLng([lat, lon]);
}

function updateLocationFields(lat, lon) {
    reverseGeocode(lat, lon);
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
            break;
    }
}

window.onload = function() {
    initMap();
};


// Get the modal
var modal = document.getElementById("imageModal");

// Get the image and insert it inside the modal
var modalImg = document.getElementById("modalImage");

document.querySelectorAll('.report img').forEach(function(image) {
    image.onclick = function(){
        modal.style.display = "block";
        modalImg.src = this.src;
    }
});

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() { 
    modal.style.display = "none";
}

// Close modal when clicking outside the image
modal.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}


document.querySelectorAll('.search-form').forEach(form => {
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('lostfound.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const successMessageContainer = this.querySelector('.search-success-message');
            if (data.success) {
                successMessageContainer.textContent = data.message;
                successMessageContainer.className = 'search-success-message success';
            } else {
                successMessageContainer.textContent = data.message;
                successMessageContainer.className = 'search-success-message error';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});



document.querySelectorAll('.save-status-button').forEach(button => {
    button.addEventListener('click', function() {
        const reportId = this.getAttribute('data-report-id');
        const status = this.previousElementSibling.querySelector('.status-dropdown').value;

        fetch('lostfound.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'update_status': true,
                'report_id': reportId,
                'status': status,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPopupMessage(data.message, );
            } else {
                showPopupMessage(data.message, false);
            }
        })
        .catch(error => {
            showPopupMessage('An error occurred. Please try again.', false);
            console.error('Error:', error);
        });
    });
});





    </script>
</body>
</html>
