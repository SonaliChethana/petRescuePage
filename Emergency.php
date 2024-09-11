<?php
session_start();
require_once 'utils/connect.php';  // Ensure this path is correct

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = [
    'success' => false,
    'message' => 'Failed to save details'
];


// Check if the user is logged in and retrieve user_id from session
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['description'], $_POST['location'], $_POST['priority'])) {
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);
        $priority = trim($_POST['priority']);

        // File upload handling
        $photos = [];
        if (!empty($_FILES['file']['name'])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
                $photos = [$target_file];
            } else {
                $response['message'] = 'Failed to upload file';
                echo json_encode($response);
                exit;
            }
        }

        $photos_serialized = serialize($photos);

        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO emergencyreport (description, location, photos, priority, user_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $response['message'] = 'Prepare failed: ' . htmlspecialchars($conn->error);
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param("ssssi", $description, $location, $photos_serialized, $priority, $user_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Details successfully saved!';
        } else {
            $response['message'] = 'Failed to save details: ' . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } elseif (isset($_POST['report_id'], $_POST['status'])) {
        $report_id = intval($_POST['report_id']);
        $status = trim($_POST['status']);

         // Check if report_id and status are valid
         if ($report_id > 0 && !empty($status)) {

        // Update status in the database
        $stmt = $conn->prepare("UPDATE emergencyreport SET status = ? WHERE id = ?");
        if ($stmt === false) {
            $response['message'] = 'Prepare failed: ' . htmlspecialchars($conn->error);
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param("si", $status, $report_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Status successfully updated!';
        } else {
            $response['message'] = 'Failed to update status: ' . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid report_id or status';
    }
} else {
    $response['message'] = 'Required fields missing';
}

    echo json_encode($response);
    exit;
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Rescue Form</title>
    <link rel="stylesheet" href="css/emergency.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



    <style>

        body{
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* Header */
        .header {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('images/bg-1.jpg') no-repeat center center/cover; /* Gradient overlay with background image */
            color: #fff; /* Text color for readability */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
    position: relative; /* Position relative for overlay effect */
    z-index: 2; /* Ensure it is above the hero content */
    display: flex;
    justify-content: space-between; /* Space out logo and navigation */
    align-items: center; /* Center-align header items vertically */
    animation: faeInDown 1s ease-out; /* Animation for header */
    max-width: 100%;
    overflow: hidden; /* Prevents horizontal scroll */

}

.header-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap; /* Wrap items on smaller screens */

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
    border: 3px ; /* Border around the logo */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Enhanced shadow for depth */
}

.title {
    font-family: 'Poppins', sans-serif;
    font-size: 2.5em; /* Larger font size for emphasis */
    color: #f4be77; /* Warm color */
    text-transform: uppercase; /* Capitalize for emphasis */
    display: flex;
    align-items: center;

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


}

.nav-list {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;

    
}

.nav-item {
    margin-left: 10px; /* Space between nav items */
}
/* Example back button */
.back-button {
    margin: 0; /* Remove any extra margin */
    padding: 10px 20px; /* Adjust padding if necessary */
    box-sizing: border-box; /* Include padding and border in element's width */
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


/* Responsive Styles */

/* Tablets and below (768px and under) */
@media (max-width: 768px) {
    .header {
        padding: 15px;
        flex-direction: column; /* Stack items vertically */
        text-align: center;
    }

    .logo {
        font-size: 1em;
        margin-bottom: 10px;
    }

    .logo img {
        max-width: 80px; /* Reduce logo size */
    }

    .title {
        font-size: 1.8em;
        margin-bottom: 10px;
    }

    .title::before, .title::after {
        font-size: 1em; /* Adjust icon size */
    }

    .nav {
        flex-direction: column; /* Stack navigation items vertically */
        width: 100%; /* Ensure full width */
        margin-top: 10px;
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        justify-content: center;
        width: 100%; /* Ensure it takes full width */
        align-items: center; /* Center-align items */
    }

    .nav-item {
        margin-left: 0;
        margin-bottom: 10px;
    }

    .nav-link {
        font-size: 14px; /* Slightly reduce font size */
        padding: 8px 15px;
    }
}

/* Mobile Devices (480px and under) */
@media (max-width: 480px) {
    .header {
        padding: 10px;
    }

    .logo {
        font-size: 0.9em;
        margin-bottom: 5px;
    }

    .logo img {
        max-width: 60px; /* Further reduce logo size */
    }

    .title {
        font-size: 1.5em;
        margin-bottom: 10px;
    }

    .title::before, .title::after {
        font-size: 0.9em; /* Adjust icon size */
    }

    .nav {
        flex-direction: column; /* Stack navigation items vertically */
        width: 100%; /* Ensure full width */
        margin-top: 10px;
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        width: 100%; /* Ensure it takes full width */
        align-items: center; /* Center-align items */
    }

    .nav-item {
        margin-left: 0;
        margin-bottom: 10px;
    }

    .nav-link {
        font-size: 12px; /* Reduce font size */
        padding: 7px 10px;
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

            .emergency-rescue-container {
                width: 100%;
                max-width: 700px;
                margin: 30px auto;
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                }

                .tabs {
                display: flex;
                background-color: #007bff;
                border-bottom: 1px solid #ddd;
                }

                .tab-button {
                flex: 1;
                padding: 15px;
                cursor: pointer;
                text-align: center;
                background-color: #007bff;
                color: #fff;
                border: none;
                outline: none;
                transition: background-color 0.3s ease, color 0.3s ease;
                font-weight: bold;
                }

                .tab-button.active {
                background-color: #0056b3;
                color: #fff;
                }

                .tab-button:hover {
                background-color: #0056b3;
                }

                .content {
                padding: 30px 20px;
                background-color: #fff;
                }

                .content-section {
                display: none;
                }

                .content-section.active {
                display: block;
                }

                form {
                display: flex;
                flex-direction: column;
                }

                form h2 {
                margin-bottom: 20px;
                color: #007bff;
                font-size: 24px;
                text-align: center;
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
                }

                form label {
                margin-top: 15px;
                font-weight: bold;
                color: #333;
                }

                form input,
                form textarea {
                padding: 12px 15px;
                margin-top: 8px;
                border-radius: 10px;
                border: 1px solid #ddd;
                background-color: #f5f5f5;
                font-size: 16px;
                }

                form input:focus,
                form textarea:focus {
                border-color: #007bff;
                outline: none;
                background-color: #fff;
                }

                form button {
                margin-top: 25px;
                padding: 12px 20px;
                background-color: #28a745;
                color: white;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                font-size: 16px;
                transition: background-color 0.3s ease;
                }

                form button:hover {
                background-color: #218838;
                }

                .vet-clinic {
                margin-bottom: 25px;
                padding: 15px;
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 10px;
                }

                .contact-button {
                display: inline-block;
                margin-top: 10px;
                padding: 10px 15px;
                background-color: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s ease;
                }

                .contact-button:hover {
                background-color: #0056b3;
                }


                .page-header {
                    text-align: center;
                    background: linear-gradient(135deg, #ff6f61, #d32f2f); /* Gradient background with urgent red tones */
                    color: white;
                    padding: 40px 20px; /* Increased padding for a more substantial header */
                    border-radius: 10px;
                    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3); /* Added shadow for depth */
                    position: relative;
                    overflow: hidden;
                }

                .page-header h1 {
                    margin: 0;
                    font-size: 48px; /* Larger, bolder font for the title */
                    font-weight: 700;
                    text-transform: uppercase; /* Capitalized letters for emphasis */
                    letter-spacing: 3px; /* Increased spacing between letters */
                    z-index: 2;
                    position: relative;
                }

                .page-header p {
                    margin-top: 15px;
                    font-size: 22px; /* Slightly larger and more readable subtitle */
                    font-weight: 300;
                    font-style: italic;
                    opacity: 0.9;
                    z-index: 2;
                    position: relative;
                }

                .page-header::before {
                    content: '';
                    position: absolute;
                    top: -50%;
                    left: -50%;
                    width: 200%;
                    height: 200%;
                    background: rgba(255, 255, 255, 0.1);
                    transform: rotate(45deg);
                    z-index: 1;
                }

                .page-header::after {
                    content: '\f0e7'; /* FontAwesome lightning bolt icon */
                    font-family: 'Font Awesome 5 Free';
                    font-weight: 900;
                    color: rgba(255, 255, 255, 0.05);
                    font-size: 150px;
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 0;
                    opacity: 0.3;
                }

                .page-header h1, .page-header p {
                    z-index: 2;
                    position: relative;
                }

                .location-wrapper {
                display: flex;
                align-items: center;
                gap: 10px;
                }

                
#location {
    flex: 1; /* Takes up available space */
    padding: 8px; /* Adds padding inside the text input */
    font-size: 16px; /* Matches the font size of the button */
}

#mapBtn {
    padding: 8px 16px; /* Adds padding inside the button */
    font-size: 16px; /* Matches the font size of the text input */
    cursor: pointer; /* Changes the cursor to a pointer on hover */
}

/* Optional: Add some basic styling for better visuals */
#mapBtn {
    background-color: #007bff; /* Blue background color */
    color: white; /* White text color */
    border: none; /* Removes default border */
    border-radius: 4px; /* Rounded corners */
    margin-bottom: 40px; /* Adds space below the button */
}

#mapBtn:hover {
    background-color: #0056b3; /* Darker blue on hover */
}
                .map-container {
                height: 400px;
                width: 100%;
                margin-top: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                }

                .notification {
    display: none; /* Hidden by default, will be shown via JavaScript */
    padding: 30px; /* Increased padding for a larger message area */
    border-radius: 10px; /* More rounded corners for a prominent look */
    font-size: 20px; /* Larger font size for better visibility */
    position: fixed;
    top: 50%; /* Center vertically */
    left: 50%; /* Center horizontally */
    transform: translate(-50%, -50%); /* Adjust for perfect centering */
    z-index: 1000; /* Ensure it stays on top of other content */
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); /* Larger shadow for emphasis */
    width: 400px; /* Fixed width to ensure consistency */
    max-width: 90%; /* Responsive on smaller screens */
    text-align: center; /* Centers the text inside */
    background-color: #fff; /* White background for better contrast */
    border: 1px solid #ddd; /* Light border to delineate the notification */
}

.notification.success {
    background-color: #d4edda; /* Light green background */
    color: #155724; /* Dark green text color */
    border: 1px solid #c3e6cb; /* Slightly darker border */
}

.notification.error {
    background-color: #f8d7da; /* Light red background */
    color: #721c24; /* Dark red text color */
    border: 1px solid #f5c6cb; /* Slightly darker border */
}

/* General styling for the emergency reports container */
.emergency-reports {
    font-family: 'Roboto', sans-serif;
    background-color: #f0f2f5;
    padding: 25px;
    border-radius: 10px;
    max-width: 900px;
    margin: 0 auto;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

/* Header styling */
.emergency-reports h2 {
    text-align: center;
    font-size: 28px;
    color: #2c3e50;
    margin-bottom: 30px;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Styling for each individual report */
.report {
    background-color: #ffffff;
    margin-bottom: 30px;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.report:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Styling for the report image */
.report img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Styling for the report details */
.report-details {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Styling for the report description */
.report-details h3 {
    font-size: 22px;
    margin: 0;
    color: #2c3e50;
    font-weight: bold;
    border-bottom: 2px solid #e74c3c;
    padding-bottom: 10px;
}

/* Styling for the report location */
.report-details p {
    font-size: 22px; /* Larger font size for better visibility */
    color: #000000; /* Dark color for high contrast */
    margin: 10px 0 20px;
    font-weight: 600; /* Semi-bold to emphasize the text */
    letter-spacing: 0.5px; /* Slightly increased letter spacing for readability */
    text-transform: uppercase; /* Uppercase to make the text stand out more */
    line-height: 1.4; /* Improved line height for readability */
}


/* Styling for the status update section */
.status-update {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background-color: #ecf0f1;
    border-radius: 8px;
    border-left: 4px solid #3498db;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Styling for the status select dropdown */
.status-select {
    flex: 1;
    padding: 10px;
    border: 2px solid #3498db;
    border-radius: 4px;
    background-color: #ffffff;
    font-size: 16px;
    color: #2c3e50;
    outline: none;
    transition: border-color 0.3s ease;
    margin-right: 10px;
}

.status-select:focus {
    border-color: #2980b9;
}

/* Styling for the save button */
.save-status-button {
    padding: 10px 20px;
    background-color: #e74c3c;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.save-status-button:hover {
    background-color: #c0392b;
}

/* Styling for no reports message */
.emergency-reports p {
    text-align: center;
    color: #0e1113;
    font-size: 18px;
    font-style: italic;
}

/* Styling for the priority level selection */
.priority-wrapper {
    margin: 15px 0;
}

.priority-wrapper select {
    padding: 10px 15px;
    border: 2px solid #ccc;
    border-radius: 8px;
    background-color: #ffffff;
    font-size: 16px;
    color: #333;
    outline: none;
    cursor: pointer;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    width: 100%; /* Full width for better alignment */
}

/* Styling for the dropdown options */
.priority-wrapper option[value="low"] {
    background-color: #d4edda;
    color: #155724;
}

.priority-wrapper option[value="medium"] {
    background-color: #fff3cd;
    color: #856404;
}

.priority-wrapper option[value="high"] {
    background-color: #ffe5e5;
    color: #721c24;
}

.priority-wrapper option[value="urgent"] {
    background-color: #f8d7da;
    color: #721c24;
}

.priority-wrapper select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Styling for the dropdown options */
.priority-wrapper option {
    padding: 10px;
}

/* Styling for the report priority */
.report-details .priority {
    font-size: 18px;
    color: #e67e22; /* A bright color for visibility */
    margin: 10px 0;
    font-weight: bold;
    text-transform: uppercase;
}

.priority.urgent, .priority.high {
    color: red;
    font-weight: bold;
}

.priority.medium {
    color: orange; /* Choose another color for medium priority */
    font-weight: bold;
}

.priority.low {
    color: green; /* Choose another color for low priority */
    font-weight: bold;
}

/* General section styling */
.advice-section {
    background-color: #f4f9f9;
    padding: 30px;
    margin: 0 auto;
    width: 85%;
    max-width: 700px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    border: 1px solid #ddd;
}

/* Heading styling */
.advice-section h2 {
    font-size: 2.2rem;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
    text-transform: uppercase;
}

/* Content and items styling */
.advice-content {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.advice-item {
    display: flex;
    align-items: flex-start;
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e0e0e0;
}

.advice-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.icon-container {
    flex-shrink: 0;
    margin-right: 20px;
}

.icon-container img {
    width: 70px; /* Increased icon size */
    height: 70px; /* Maintain aspect ratio */
    object-fit: cover;
    transition: transform 0.3s;
}

.icon-container img:hover {
    transform: scale(1.1);
}

.text-container {
    flex: 1;
}

.text-container h3 {
    font-size: 1.4rem;
    color: #007bff;
    margin: 0 0 10px;
    font-weight: 600;
}

.text-container p {
    font-size: 1.1rem;
    color: #555;
    line-height: 1.6;
}

/* Additional content styling */
.additional-content {
    margin-top: 30px;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

.additional-content h2 {
    font-size: 1.7rem;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.additional-content p {
    font-size: 1.1rem;
    color: #555;
    line-height: 1.6;
    margin-bottom: 15px;
}

.header-buttons {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.header-button {
    display: inline-block;
    padding: 15px 30px;
    margin: 0 10px;
    font-size: 18px;
    font-weight: bold;
    color: #fff;
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    border: none;
    border-radius: 50px;
    text-align: center;
    text-transform: uppercase;
    text-decoration: none;
    box-shadow: 0px 10px 20px rgba(255, 75, 43, 0.5);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.header-button:hover {
    background: linear-gradient(45deg, #ff4b2b, #ff416c);
    box-shadow: 0px 15px 25px rgba(255, 75, 43, 0.75);
    transform: translateY(-5px);
}

.header-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 300%;
    height: 300%;
    background: rgba(255, 255, 255, 0.3);
    opacity: 0;
    transition: all 0.5s ease;
    transform: rotate(45deg) translate(-300%, -300%);
    z-index: 0;
}

.header-button:hover::before {
    opacity: 1;
    transform: rotate(45deg) translate(0%, 0%);
}

.header-button span {
    position: relative;
    z-index: 1;
}

/* Style for the Responsibility Section */
.responsibility-section {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.responsibility-section label {
    font-weight: bold;
    margin-bottom: 10px;
    display: block;
    color: #333;
}

.responsibility-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 16px;
    color: #555;
}

.additional-info-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 16px;
    color: #555;
}

.save-responsibility-button {
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.save-responsibility-button:hover {
    background-color: #0056b3;
}
/* Advanced Style for the assigned entity section */
.assigned-entity {
    font-size: 14px; /* Reduced font size for a more modern look */
    font-weight: 600; /* Slightly lighter than bold for a modern feel */
    color: #333333; /* Dark gray text color for better contrast */
    background: linear-gradient(135deg, #f8f2f0, #ffbebe); /* Light gradient background for a soft effect */
    padding: 12px 20px; /* Increased padding for a more spacious layout */
    border-radius: 8px; /* More pronounced rounded corners */
    border: 1px solid #6c1000; /* Light gray border for subtle definition */
    margin-bottom: 15px; /* Increased space below the section */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Light shadow for a subtle lifted effect */
    text-align: center; /* Center-align the text */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Modern font for a contemporary look */
    letter-spacing: 0.5px; /* Slightly increased letter spacing for readability */
}




/* Message container styling */
.message-container {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    max-width: 400px;
    display: none; /* Initially hidden */
    z-index: 9999; /* Ensure it is on top of other content */
    text-align: center;
}

/* Message content styling */
.message-content {
    background-color: #fff;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    font-size: 16px;
}

/* Success message styling */
.message-success {
    color: #28a745;
    border: 1px solid #28a745;
}

/* Error message styling */
.message-error {
    color: #dc3545;
    border: 1px solid #dc3545;
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
                    <li class="nav-item">
                        <?php
                        // Check the 'from' parameter in the URL
                        $from = isset($_GET['from']) ? $_GET['from'] : 'homepage';

                        // Determine the correct URL for the back button
                        if ($from === 'dashboard') {
                            $backUrl = 'dashboard.php';
                        } else {
                            $backUrl = 'index.php'; // Assuming index.php is your homepage
                        }
                        ?>
                        <!-- Output the back button with the dynamic URL -->
                        <a href="<?php echo $backUrl; ?>" class="nav-link home-button">Back</a>  
                    </li>
                    
                </ul>
                
            </nav>
        </div>
    </header>


    <div class="page-header">
        <h1>Emergency Rescue Assistance</h1>
        <p>Providing immediate help for animals in distress</p>

    </div>
    <div class="header-buttons">
        <button class="header-button" onclick="scrollToSection('formSection')">Emergency Rescue Form</button>
        <button class="header-button" onclick="scrollToSection('reportList')">Emergency Reports</button>
    </div>

    <br />
    <br />

    <div class="advice-section">
    <h2>What to Do in an Emergency Situation with a Stray Dog</h2>
    <div class="advice-content">
        <!-- Advice Item 1 -->
        <div class="advice-item">
            <div class="icon-container">
                <img src="images/calm.png" alt="Stay Calm">
            </div>
            <div class="text-container">
                <h3>Stay Calm</h3>
                <p>It’s important to remain calm and avoid sudden movements. Stray dogs can be unpredictable, and a calm demeanor can help keep the situation under control.</p>
            </div>
        </div>
        <!-- Advice Item 2 -->
        <div class="advice-item">
            <div class="icon-container">
                <img src="images/assess.png" alt="Assess the Dog">
            </div>
            <div class="text-container">
                <h3>Assess the Dog's Condition</h3>
                <p>Check if the dog is injured, aggressive, or scared. Look for signs of illness or distress, such as limping, bleeding, or visible fear.</p>
            </div>
        </div>
        <!-- Advice Item 3 -->
        <div class="advice-item">
            <div class="icon-container">
                <img src="images/contact.png" alt="Contact Authorities">
            </div>
            <div class="text-container">
                <h3>Contact Authorities</h3>
                <p>If the dog seems aggressive or injured, contact local animal control or a rescue organization immediately.</p>
            </div>
        </div>
    </div>
    <div class="additional-content">
        <h2>Additional Tips</h2>
        <p>Always carry a mobile phone with emergency numbers saved. If the dog is approachable, consider taking them to the nearest vet or shelter for further assistance.</p>
    </div>
</div>





            <div id="emergency-rescue-container" class="emergency-rescue-container">
        <div class="tabs">
            <button class="tab-button active" onclick="showSection('formSection')">Emergency Rescue Form</button>
            <button class="tab-button" onclick="showSection('vetContactSection')">Quick Vet Contact</button>
        </div>

        <div class="content">
            <!-- Emergency Rescue Form Section -->
            <div id="formSection" class="content-section">
            <form id="emergencyForm" method="POST" enctype="multipart/form-data">
            <h2>Emergency Rescue Form</h2>
                

                <label for="location">Location:</label>
                <div class="location-wrapper">
                    <input type="text" id="location" name="location" required placeholder="Enter location manually">
                    <button type="button" id="mapBtn">Select Location on Map</button>
                </div>
                
                <div id="map" class="map-container"></div>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>

                <label for="priority">Priority Level:</label>
                <div class="priority-wrapper">
                    <select id="priority" name="priority" required>
                        <option value="" disabled selected>Select priority level</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <label for="file">Upload Photo/Video:</label>
                <input type="file" id="file" name="file">

                <button type="submit">Submit</button>
            </form>
            </div>

    <!-- Quick Vet Contact Section -->
    <div id="vetContactSection" class="content-section" style="display: none;">
      <h2>Quick Vet Contact</h2>
      <div class="vet-clinic">
        <h3>City Vet Clinic</h3>
        <p>123 Main St, Springfield</p>
        <a href="tel:+1234567890" class="contact-button">Call Now</a>
        <a href="https://maps.google.com" target="_blank" class="contact-button">Get Directions</a>
      </div>

      <div class="vet-clinic">
        <h3>Happy Paws Veterinary</h3>
        <p>456 Elm St, Springfield</p>
        <a href="tel:+0987654321" class="contact-button">Call Now</a>
        <a href="https://maps.google.com" target="_blank" class="contact-button">Get Directions</a>
      </div>

      <!-- Add more vet clinics as needed -->
    </div>
  </div>
</div>

    <div class="emergency-reports">
    <h2>Emergency Reports</h2>
    <div id="reportList">
        <?php
        // Fetch reports from the database
        require_once 'utils/connect.php'; // Ensure the path is correct

        $sql = "SELECT id, description, location, photos, status, priority, responsible_entity FROM emergencyreport"
            . " ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low')"; // Order by priority level
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $photos = unserialize($row['photos']);
                echo '<div class="report">';
                if (!empty($photos)) {
                    echo '<img src="' . htmlspecialchars($photos[0]) . '" alt="Dog Photo">';
                }
                echo '<div class="report-details">';
                echo '<h3>Description: ' . htmlspecialchars($row['description']) . '</h3>';
                echo '<p>Location: ' . htmlspecialchars($row['location']) . '</p>';
                echo '<p class="priority ' . htmlspecialchars($row['priority']) . '">Priority: ' . htmlspecialchars($row['priority']) . '</p>';

                

                // Add Status Update Dropdown
                echo '<div class="status-update">';
                echo '<select class="status-select" data-report-id="' . htmlspecialchars($row['id']) . '">';
                echo '<option value="status" ' . ($row['status'] == 'status' ? 'selected' : '') . '>Status</option>';
                echo '<option value="rescued" ' . ($row['status'] == 'rescued' ? 'selected' : '') . '>Rescued</option>';
                echo '<option value="rescue in progress" ' . ($row['status'] == 'rescue in progress' ? 'selected' : '') . '>Rescue in Progress</option>';
                echo '</select>';
                echo '<button class="save-status-button" data-report-id="' . htmlspecialchars($row['id']) . '">Save</button>';
                echo '</div>';

                  // Responsibility Claim Section
                echo '<div class="responsibility-section">';
                echo '<label for="responsibility-' . htmlspecialchars($row['id']) . '">Claim Responsibility:</label>';
                echo '<select id="responsibility-' . htmlspecialchars($row['id']) . '" class="responsibility-select" data-report-id="' . htmlspecialchars($row['id']) . '">';
                echo '<option value="">Select Your Entity</option>';
                echo '<option value="volunteer-' . htmlspecialchars($_SESSION['username']) . '">' . htmlspecialchars($_SESSION['username']) . ' Volunteer</option>';
                echo '<option value="shelter-' . htmlspecialchars($_SESSION['shelter_name']) . '">' . htmlspecialchars($_SESSION['shelter_name']) . ' Animal Shelter</option>';
                echo '<option value="vet_clinic-' . htmlspecialchars($_SESSION['vet_clinic_name']) . '">' . htmlspecialchars($_SESSION['vet_clinic_name']) . ' Vet Clinic</option>';
                echo '<option value="other-">Other</option>';
                echo '</select>';
                // Input fields for additional details
                echo '<input type="text" id="volunteer-info-' . htmlspecialchars($row['id']) . '" class="additional-info-input" data-entity-type="volunteer" style="display:none;" placeholder="Enter volunteer details">';
                echo '<input type="text" id="shelter-info-' . htmlspecialchars($row['id']) . '" class="additional-info-input" data-entity-type="shelter" style="display:none;" placeholder="Enter shelter details">';
                echo '<input type="text" id="vet_clinic-info-' . htmlspecialchars($row['id']) . '" class="additional-info-input" data-entity-type="vet_clinic" style="display:none;" placeholder="Enter vet clinic details">';
                echo '<input type="text" id="other-info-' . htmlspecialchars($row['id']) . '" class="additional-info-input" data-entity-type="other" style="display:none;" placeholder="Enter name or details">';

                echo '<button class="save-responsibility-button" data-report-id="' . htmlspecialchars($row['id']) . '">Claim Responsibility</button>';

                // Display current assigned entity if exists
                if ($row['responsible_entity']) {
                    echo '<p class="assigned-entity">Currently claimed by: ' . htmlspecialchars($row['responsible_entity']) . '</p>';
                }

                echo '</div>'; // Responsibility section end


                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No reports found.</p>';
        }

        $conn->close();
        ?>
    </div>
</div>

<!-- Message Container -->
<div id="message-container" class="message-container">
    <div id="message-content" class="message-content"></div>
</div>






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


    <script>

document.addEventListener('DOMContentLoaded', function() {
    // Show the default section (Emergency Rescue Form) on page load
    showSection('formSection');

    document.querySelector('#emergencyForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('Emergency.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            displayMessage(data.message, data.success ? 'success' : 'error');
        })
        .catch(error => {
            console.error('Error:', error);
            displayMessage('Failed to save details. Please try again.', 'error');
        });
    });

    function displayMessage(message, type) {
        const notification = document.getElementById('notification');
        if (!notification) {
            const newNotification = document.createElement('div');
            newNotification.id = 'notification';
            newNotification.className = `notification ${type}`;
            document.body.appendChild(newNotification);
        } else {
            notification.className = `notification ${type}`;
        }
        const notificationElement = document.getElementById('notification');
        notificationElement.textContent = message;
        notificationElement.style.display = 'block';

        setTimeout(() => {
            notificationElement.style.display = 'none';
        }, 3000);
    }

    // Save status update button click event
    document.querySelectorAll('.save-status-button').forEach(button => {
        button.addEventListener('click', function() {
            const reportId = this.getAttribute('data-report-id');
            const statusSelect = this.previousElementSibling;
            const status = statusSelect.value;

            if (status && reportId) {
                updateStatus(reportId, status);
            }
        });
    });

    function updateStatus(reportId, status) {
        fetch('Emergency.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `report_id=${encodeURIComponent(reportId)}&status=${encodeURIComponent(status)}`
        })
        .then(response => response.json())
        .then(data => {
            displayMessage(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                const statusSelect = document.querySelector(`select[data-report-id="${reportId}"]`);
                if (statusSelect) {
                    statusSelect.value = status;  // Update the UI to reflect the new status
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            displayMessage('Failed to update status. Please try again.', 'error');
    });

    }
});

 function showSection(sectionId) {
    // Hide all content sections
    document.querySelectorAll('.content-section').forEach(function(section) {
        section.style.display = 'none';
    });

    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(function(button) {
        button.classList.remove('active');
    });

    // Show the selected content section
    document.getElementById(sectionId).style.display = 'block';

    // Add active class to the selected tab button
    document.querySelector(`.tab-button[onclick="showSection('${sectionId}')"]`).classList.add('active');
}

        


           
            
    // Function to show a specific section
    function showSection(sectionId) {
        // Hide all content sections
        document.querySelectorAll('.content-section').forEach(function(section) {
            section.style.display = 'none';
        });

        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(function(button) {
            button.classList.remove('active');
        });

        // Show the selected content section
        document.getElementById(sectionId).style.display = 'block';

        // Add active class to the selected tab button
        document.querySelector(`.tab-button[onclick="showSection('${sectionId}')"]`).classList.add('active');
    }

    // Initially display the form section
    showSection('formSection');




    
    let map, marker;

    function initMap() {
        map = L.map('map').setView([6.9271, 79.8612], 13); // Default to Sri Lanka coordinates

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        marker = L.marker([6.9271, 79.8612]).addTo(map);

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            marker.setLatLng([lat, lng]);

            // Reverse geocoding to get location name
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    const locationName = data.display_name;
                    document.getElementById('location').value = locationName;
                })
                .catch(error => console.error('Error:', error));
        });
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        initMap();
    });


    function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
}

// Function to display messages
function showMessage(type, text) {
    const messageContainer = document.getElementById('message-container');
    const messageContent = document.getElementById('message-content');

    // Clear previous message
    messageContent.className = 'message-content'; // Reset class
    messageContent.classList.add(type === 'success' ? 'message-success' : 'message-error');
    messageContent.textContent = text;

    // Show message container
    messageContainer.style.display = 'block';
    
    // Hide the message container after a few seconds
    const timeoutDuration = type === 'success' ? 5000 : 3000; // Success message for 5 seconds, error for 3 seconds
    setTimeout(() => {
        messageContainer.style.display = 'none';
    }, timeoutDuration); // Adjust timeout here
}

// JavaScript to toggle the additional info input fields based on selected responsibility type
document.querySelectorAll('.responsibility-select').forEach(select => {
    select.addEventListener('change', function() {
        const reportId = this.dataset.reportId;
        const selectedValue = this.value;

        // Hide all additional info inputs
        document.querySelectorAll('.additional-info-input').forEach(input => {
            input.style.display = 'none';
            input.value = ''; // Clear the input field
        });

        // Show the relevant input field based on selection
        if (selectedValue) {
            let inputId = '';
            if (selectedValue === 'other') {
                inputId = 'other-entity-' + reportId;
            } else {
                inputId = selectedValue.split('-')[0] + '-info-' + reportId;
            }
            
            console.log('Selected Value:', selectedValue); // Debugging
            console.log('Input ID:', inputId); // Debugging
            
            const inputElement = document.getElementById(inputId);
            if (inputElement) {
                inputElement.style.display = 'block';
            } else {
                console.log('Element not found for ID:', inputId); // Debugging
            }
        }
    });
});

// Save responsibility via AJAX
document.querySelectorAll('.save-responsibility-button').forEach(button => {
    button.addEventListener('click', function() {
        const reportId = this.dataset.reportId;
        const responsibility = document.getElementById('responsibility-' + reportId).value;
        let additionalInfo = '';
        let otherEntity = '';

        // Determine the input field for additional info
        let inputId = '';
        if (responsibility && responsibility !== 'other') {
            inputId = responsibility.split('-')[0] + '-info-' + reportId;
            additionalInfo = document.getElementById(inputId) ? document.getElementById(inputId).value : '';
        } else if (responsibility === 'other') {
            inputId = 'other-entity-' + reportId;
            otherEntity = document.getElementById(inputId).value;
        }

        // AJAX request to save responsibility
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'assign_responsibility.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                showMessage('success', 'Responsibility claimed successfully!');
                setTimeout(() => {
                    location.reload(); // Refresh after message disappears
                }, 5000); // Delay refresh to allow the success message to be visible
            } else {
                showMessage('error', 'Error saving responsibility.');
            }
        };
        xhr.send('report_id=' + reportId + '&responsibility=' + encodeURIComponent(responsibility) + '&additional_info=' + encodeURIComponent(additionalInfo) + '&other_entity=' + encodeURIComponent(otherEntity));
    });
});


    </script>
</body>
</html>
