<?php
session_start();
require_once 'utils/connect.php';


// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = [
    'success' => false,
    'message' => 'An error occurred'
];

// Check if the user is logged in and retrieve user_id from session
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['description'], $_POST['location'], $_POST['behaviour'])) {
        $description = $_POST['description'];
        $location = $_POST['location'];
        $behaviour = $_POST['behaviour'];

       


        // File upload handling
        $photos = [];
        if (!empty($_FILES['photos']['name'][0])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            foreach ($_FILES['photos']['name'] as $key => $name) {
                $target_file = $target_dir . basename($name);
                if (move_uploaded_file($_FILES['photos']['tmp_name'][$key], $target_file)) {
                    $photos[] = $target_file;
                } else {
                    $response['message'] = 'Failed to upload file: ' . htmlspecialchars($name);
                    echo json_encode($response);
                    exit;
                }
            }
        }

        $photos_serialized = serialize($photos);

        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO reportstray (description, location, photos, behaviour, user_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt === false) {
            error_log('Prepare failed: ' . htmlspecialchars($conn->error));
            $response['message'] = 'Prepare failed: ' . htmlspecialchars($conn->error);
            echo json_encode($response);
            exit;
        }
        
        $stmt->bind_param("ssssi", $description, $location, $photos_serialized, $behaviour, $user_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Details successfully saved!';
        } else {
            $response['message'] = 'Failed to save details: ' . htmlspecialchars($stmt->error);
        }
        
        $stmt->close();
    } elseif (isset($_POST['report_id'], $_POST['status'])) {
        $report_id = $_POST['report_id'];
        $status = $_POST['status'];

        // Check if report_id and status are valid
        if ($report_id > 0 && !empty($status)) {

        // Update status in the database
        $stmt = $conn->prepare("UPDATE reportstray SET status = ? WHERE id = ?");
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
            $response['message'] = 'Failed to update status: ' .  htmlspecialchars($stmt->error);
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
    <title>Report a Stray Dog</title>
    <link rel="stylesheet" href="css/reportStray.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />



    <style>
       

        /* Header */
        .header {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('images/bg-1.jpg') no-repeat center center/cover; /* Gradient overlay with background image */
            color: #fff; /* Text color for readability */
    padding: 0px 0px; /* Padding around the header content */
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
    justify-content: space-between; /* Space between logo/title and nav */
    align-items: center;
    width: 100%;
    padding: 0 20px; /* Add some padding on the sides for spacing */

}

/* Align logo and title to the left */
.header-left {
    display: flex;
    align-items: center;
   

}
.hader-left img{
    margin-right: 15px;
}
.logo {
    display: flex;
    align-items: center;
    font-family: 'Poppins', sans-serif;
    font-size: 1.2em; /* Adjusted font size */
    color: #ff7043; /* Matching warm color */
    text-transform: uppercase; /* Capitalize for emphasis */

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
    margin-left: auto; /* Ensure the nav stays to the right */
    gap: 20px; /* Add space between nav items if needed */
}

.nav-list {
    display: flex;
    justify-content: flex-end; /* Push navigation buttons to the right */
    list-style: none;
    margin: 0;
    padding: 0;

    
}

.nav-item {
    margin-left: 10px; /* Space between nav items */
}

.home-button {
    margin-left: auto; /* Adjust this value to push the Home button further right */
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



/* Responsive Styles */
@media (max-width: 1200px) {
    .title {
        font-size: 2em; /* Adjust title size for medium screens */
        margin-right: 50px; /* Reduce space between title and nav */
    }

    .logo img {
        max-width: 80px; /* Adjust logo size for medium screens */
    }
}

@media (max-width: 992px) {
    .header {
        flex-direction: column; /* Stack items vertically */
        padding: 10px; /* Adjust padding */
    }

    .header-left {
        margin-bottom: 10px; /* Space between logo/title and nav */
    }

    .logo img {
        max-width: 60px; /* Further adjust logo size for smaller screens */
    }

    .title {
        font-size: 1.8em; /* Further adjust title size for smaller screens */
        margin-right: 0; /* Remove margin for smaller screens */
    }

    .nav {
        width: 100%; /* Full width for navigation */
        justify-content: center; /* Center-align navigation items */
        margin-left: 0; /* Remove extra margin */
    }

    .nav-list {
        flex-direction: column; /* Stack nav items vertically */
        align-items: center; /* Center-align nav items */
    }

    .nav-item {
        margin-left: 0; /* Remove margin between items */
        margin-bottom: 10px; /* Space between items */
    }
}

@media (max-width: 768px) {
    .title {
        font-size: 1.5em; /* Further adjust title size */
    }

    .logo img {
        max-width: 50px; /* Further adjust logo size */
    }
}

@media (max-width: 576px) {
    .header {
        padding: 10px 5px; /* Adjust padding for very small screens */
    }

    .title {
        font-size: 1.2em; /* Further adjust title size */
    }

    .logo img {
        max-width: 40px; /* Further adjust logo size */
    }
}
          

                    /* Title Section Styling */
                    .report-title {
                background: linear-gradient(135deg, rgba(161, 87, 13, 0.9), rgba(229, 159, 30, 0.9)); /* Add a texture or pattern */
                padding: 40px;
                text-align: center;
                border-radius: 20px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
                margin-bottom: 40px;
                position: relative;
                overflow: hidden;
                z-index: 1;
            }

            .report-title::before {
                content: '';
                position: absolute;
                top: 0;
                left: -50%;
                width: 200%;
                height: 100%;
                background: rgba(255, 255, 255, 0.1);
                transform: skewX(-20deg);
                transition: all 0.7s ease;
                z-index: 2;
            }

            .report-title:hover::before {
                left: 100%;
            }

            .report-title h2 {
                font-size: 3rem;
                color: white;
                text-transform: uppercase;
                letter-spacing: 4px;
                margin: 0;
                position: relative;
                z-index: 3;
                font-family: 'Oswald', sans-serif; /* Use a unique font */
                text-shadow: 2px 4px 6px rgba(0, 0, 0, 0.5);
                animation: fadeInTitle 1s ease-in-out;
            }

            /* Keyframes for title animation */
            @keyframes fadeInTitle {
                0% {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Responsive styles */
            @media (max-width: 1200px) {
                .report-title {
                    padding: 30px;
                }

                .report-title h2 {
                    font-size: 2.5rem;
                    letter-spacing: 3px;
                }
            }

            @media (max-width: 768px) {
                .report-title {
                    padding: 20px;
                }

                .report-title h2 {
                    font-size: 2rem;
                    letter-spacing: 2px;
                }
            }

            @media (max-width: 480px) {
                .report-title {
                    padding: 15px;
                }

                .report-title h2 {
                    font-size: 1.5rem;
                    letter-spacing: 1px;
                }
            }


            /* Content Section Styling */
            .report-section {
                background: linear-gradient(135deg, #fdf2e3, #f9c190);
                border-radius: 15px;
                padding: 40px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                max-width: 700px;
                margin: 0 auto;
                font-family: 'Arial', sans-serif;
            }

            .report-content p {
                font-size: 1.1rem;
                color: #333;
                line-height: 1.7;
                margin-bottom: 20px;
            }

            .report-content ul {
                list-style: none;
                padding: 0;
                margin: 20px 0;
                text-align: left;
            }

            .report-guidelines li {
            font-size: 1rem;
            color: #c07615;
            padding-left: 30px; /* Adjust spacing to accommodate the icon */
            margin-bottom: 12px;
            position: relative;
        }

        .report-guidelines li::before {
           
            font-weight: 900;
            color: #e5b01e;
            margin-right: 10px; 
            position: absolute;
            left: 0;
            top: 50%;
        }
        .icon-location::before {
            content: "\f3c5"; /* FontAwesome map marker icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #e5b01e;
        }

        .icon-time::before {
            content: "\f073"; /* FontAwesome calendar icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #e5b01e;
        }

        .icon-description::before {
            content: "\f1b0"; /* FontAwesome paw icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #e5b01e;
        }

        .icon-features::before {
            content: "\f02c"; /* FontAwesome tag icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #e5b01e;
        }

        .icon-photos::before {
            content: "\f030"; /* FontAwesome camera icon */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: #e5b01e;
        }





        .report-button {
        background-color: #8B4513; /* SaddleBrown */
        color: #ffffff;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.1em;
        transition: background-color 0.3s, transform 0.3s;
        font-family: 'Roboto', sans-serif;
        margin: 20px 0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .report-button:hover {
        background-color: #6F4F28; /* Darker shade of brown */
        transform: scale(1.02);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }



          /* Responsive styles */
@media (max-width: 1200px) {
    .report-section {
        padding: 30px; /* Adjust padding */
        max-width: 90%; /* Adjust width */
    }

    .report-content p {
        font-size: 1rem; /* Adjust font size */
    }

    .report-guidelines li {
        font-size: 0.9rem; /* Adjust font size */
        padding-left: 25px; /* Adjust icon spacing */
    }

    .report-button {
        font-size: 1em; /* Adjust font size */
        padding: 10px 20px; /* Adjust padding */
    }
}

@media (max-width: 768px) {
    .report-section {
        padding: 20px; /* Adjust padding */
        max-width: 95%; /* Adjust width */
    }

    .report-content p {
        font-size: 0.9rem; /* Adjust font size */
    }

    .report-guidelines li {
        font-size: 0.85rem; /* Adjust font size */
        padding-left: 20px; /* Adjust icon spacing */
    }

    .report-button {
        font-size: 0.9em; /* Adjust font size */
        padding: 8px 15px; /* Adjust padding */
    }
}

@media (max-width: 480px) {
    .report-section {
        padding: 15px; /* Adjust padding */
        max-width: 100%; /* Full width */
    }

    .report-content p {
        font-size: 0.8rem; /* Adjust font size */
    }

    .report-guidelines li {
        font-size: 0.8rem; /* Adjust font size */
        padding-left: 15px; /* Adjust icon spacing */
    }

    .report-button {
        font-size: 0.8em; /* Adjust font size */
        padding: 6px 10px; /* Adjust padding */
    }
}  


            



        .form-container, .report-section{
            width: 80%;
            max-width: 800px;
            margin-bottom: 40px;
        }

        

        @media (max-width: 768px) {
            .form-container, .reports-section {
                width: 100%;
            }

            .report {
                flex-direction: column;
                align-items: flex-start;
            }

            .report img {
                width: 100%;
                height: auto;
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



        /* Form Section Styles */
        .form-section {
            width: 100%;
            max-width: 700px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #dcdcdc;
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .form-section:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* Form Image Styles */
        .form-image {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Form Title Styles */
        .form-section h2 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #ff8000;
            font-family: 'Roboto', sans-serif;
            text-align: center;
            background: linear-gradient(90deg, #ff9d00, #ffae00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            padding-bottom: 10px;
        }

        .form-section h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            width: 60%;
            height: 3px;
            background-color: #ff8000;
            transform: translateX(-50%);
        }

        /* Form Description Styles */
        .form-section p {
            margin-bottom: 20px;
            font-size: 1.1em;
            color: #666;
            font-family: 'Arial', sans-serif;
            text-align: center;
        }

        /* Input and Label Styles */
        .form-section label {
            display: block;
            margin: 15px 0 5px;
            font-weight: 600;
            color: #444;
            font-family: 'Arial', sans-serif;
        }

        .form-section input[type="text"],
        .form-section input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            margin-bottom: 20px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-section input[type="text"]:focus,
        .form-section input[type="file"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
            outline: none;
        }

        /* Radio Group Styles */
        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .radio-group label {
            font-size: 1em;
            color: #444;
            display: flex;
            align-items: center;
        }

        /* Button Styles */
        .form-section button {
            background-color: #ffaa00;
            color: #010000;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s, transform 0.3s;
            font-family: 'Roboto', sans-serif;
        }

        .form-section button:hover {
            background-color: #b35c00;
            transform: scale(1.02);
        }



.description-note {
        font-size: 0.9rem;
        color: #555;
        margin-top: 5px;
        font-family: 'Arial', sans-serif;
    }

    #map {
            height: 400px;
            width: 100%;
        }

        /* Responsive Styles for Form Section */
@media (max-width: 1200px) {
    .form-section {
        padding: 20px; /* Adjust padding for medium screens */
    }

    .form-section h2 {
        font-size: 1.8em; /* Reduce title size for medium screens */
    }

    .form-section p {
        font-size: 1em; /* Reduce description size for medium screens */
    }
}

@media (max-width: 992px) {
    .form-section {
        max-width: 90%; /* Allow form to take more width on smaller screens */
    }

    .form-section h2 {
        font-size: 1.6em; /* Further reduce title size for smaller screens */
    }

    .form-section p {
        font-size: 0.9em; /* Further reduce description size for smaller screens */
    }

    .form-image img {
        max-width: 100%; /* Ensure image is responsive */
    }

    .radio-group {
        flex-direction: column; /* Stack radio buttons vertically */
        gap: 10px; /* Reduce gap between radio buttons */
    }

    .form-section button {
        width: 100%; /* Full-width button on smaller screens */
        padding: 10px; /* Adjust padding for smaller screens */
        font-size: 1em; /* Reduce button font size */
    }
}

@media (max-width: 768px) {
    .form-section {
        padding: 15px; /* Further adjust padding for small screens */
    }

    .form-section h2 {
        font-size: 1.4em; /* Reduce title size for small screens */
    }

    .form-section p {
        font-size: 0.9em; /* Keep description size for small screens */
    }

    .form-image img {
        max-width: 100%; /* Ensure image maintains responsiveness */
    }

    .form-section input[type="text"],
    .form-section input[type="file"] {
        padding: 10px; /* Adjust padding for input fields */
    }

    .radio-group {
        gap: 5px; /* Further reduce gap between radio buttons */
    }
}

@media (max-width: 576px) {
    .form-section {
        padding: 10px; /* Adjust padding for very small screens */
    }

    .form-section h2 {
        font-size: 1.2em; /* Further reduce title size */
    }

    .form-section p {
        font-size: 0.8em; /* Further reduce description size */
    }

    .form-image img {
        max-width: 100%; /* Ensure image scales well */
    }

    .form-section input[type="text"],
    .form-section input[type="file"] {
        padding: 8px; /* Reduce padding for input fields */
    }

    .radio-group {
        gap: 5px; /* Maintain reduced gap between radio buttons */
    }

    .form-section button {
        font-size: 0.9em; /* Reduce button font size */
    }
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
.reports-section {
    font-family: 'Roboto', sans-serif;
    background-color: #f0f2f5;
    padding: 25px;
    border-radius: 10px;
    max-width: 900px;
    margin: 0 auto;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

/* Header styling */
.reports-section h2 {
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


/* Responsive Styles */
@media (max-width: 1200px) {
    .reports-section {
        padding: 20px; /* Adjust padding */
    }

    .report {
        grid-template-columns: 1fr 1.5fr; /* Adjust column ratio */
    }

    .report-details h3 {
        font-size: 20px; /* Adjust font size */
    }

    .report-details p {
        font-size: 20px; /* Adjust font size */
    }
}

@media (max-width: 992px) {
    .reports-section {
        padding: 15px; /* Reduce padding */
    }

    .report {
        grid-template-columns: 1fr; /* Stack image and details vertically */
    }

    .report img {
        height: 150px; /* Adjust image height */
    }

    .report-details h3 {
        font-size: 18px; /* Adjust font size */
    }

    .report-details p {
        font-size: 18px; /* Adjust font size */
    }

    .status-update {
        flex-direction: column; /* Stack status elements vertically */
    }

    .status-select {
        margin-right: 0; /* Remove margin-right */
        margin-bottom: 10px; /* Add margin-bottom */
    }
}

@media (max-width: 768px) {
    .report img {
        height: 120px; /* Further reduce image height */
    }

    .report-details h3 {
        font-size: 16px; /* Further reduce font size */
    }

    .report-details p {
        font-size: 16px; /* Further reduce font size */
    }

    .status-update {
        padding: 10px; /* Reduce padding */
    }

    .status-select {
        font-size: 14px; /* Adjust font size */
    }

    .save-status-button {
        padding: 8px 16px; /* Reduce button padding */
        font-size: 14px; /* Adjust font size */
    }
}

@media (max-width: 576px) {
    .reports-section {
        padding: 10px; /* Further reduce padding */
    }

    .report img {
        height: 100px; /* Further reduce image height */
    }

    .report-details h3 {
        font-size: 14px; /* Further reduce font size */
    }

    .report-details p {
        font-size: 14px; /* Further reduce font size */
    }

    .status-update {
        padding: 8px; /* Further reduce padding */
    }

    .status-select {
        font-size: 12px; /* Adjust font size */
    }

    .save-status-button {
        padding: 6px 12px; /* Further reduce button padding */
        font-size: 12px; /* Adjust font size */
    }
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
                    <li class="nav-item"><a href="Dashboard.php" class="nav-link home-button">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="notification success">
        <p><?php echo $_SESSION['success']; ?></p>
    </div>
    <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="notification error">
            <p><?php echo $_SESSION['error']; ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

            <div class="report-title">
            <h2>Report a Stray</h2>
        </div>
        <div class="report-section">
            <div class="report-content">
                <p>Help us keep your community safe by reporting stray dogs. Please provide as much detail as possible.</p>
                <ul class="report-guidelines">
                    <li><i class="fas fa-map-marker-alt"></i> Location of the sighting</li>
                    <li><i class="fas fa-calendar-alt"></i> Time and date of the sighting</li>
                    <li><i class="fas fa-paw"></i> Description of the dog (color, size, breed, etc.)</li>
                    <li><i class="fas fa-dog"></i> Behavior (friendly or aggressive)</li>
                    <li><i class="fas fa-camera"></i> Photos or videos if available</li>
                </ul>
                <p>Your report helps us take swift action and ensure the safety of both the dog and the community.</p>
                <button class="report-button" onclick="scrollToFormTitle()">Report Now</button>
            </div>
        </div>
        <br />
        <br />


        <div class="container">
            <div class="form-container">
            
                <div class="form-section">
                    

                    <div class="form-image">
                        <img src="images/straydog.jpeg" alt="Stray Dog Image">
                    </div>

                    <!-- Form for reporting a stray dog -->
                    <form id="reportForm" action="ReportStray.php" method="POST" enctype="multipart/form-data">
                        
                        <h2 id="formTitle">Report Form</h2>
                        <p>Please provide detailed information about the stray dog.</p>
                        <label for="description">Dog's Description</label>
                        <input type="text" id="description" name="description" required>
                        <p class="description-note">
                            <strong>Tip:</strong> Include details like size, color, breed (if known), visible injuries, and behavior (e.g., limping, scared).
                        </p>

                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required>
                        <button type="button" onclick="getLocation()">Use My Current Location</button>

                        <div id="map"></div>

                       

                        <label for="photos">Photos</label>
                        <input type="file" id="photos" name="photos[]" multiple accept="image/*">

                        <label>Behaviour</label>
                        <div class="radio-group">
                            <label><input type="radio" name="behaviour" value="Aggressive" required> Aggressive</label>
                            <label><input type="radio" name="behaviour" value="Friendly" required> Friendly</label>
                        </div>

                        <button type="submit">Submit Report</button>
                    </form>
                </div>
            </div>
            <div class="reports-section">
                <h2>Stray Dog Reports</h2>
                <?php
                // Fetch reports from the database
                require_once 'utils/connect.php';

                $sql = "SELECT id, description, location, photos, behaviour, status, responsible_entity FROM reportstray";
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
                        echo '<p>Behaviour: ' . htmlspecialchars($row['behaviour']) . '</p>';
                        echo '</div>';
                        echo '<div class="status-container">';
                        echo '<div class="status-dropdown">';
                        echo '<select class="status-select" data-report-id="' . htmlspecialchars($row['id']) . '">';
                        echo '<option value="status" ' . ($row['status'] == 'status' ? 'selected' : '') . '>Status</option>';
                        echo '<option value="rescued" ' . ($row['status'] == 'rescued' ? 'selected' : '') . '>Rescued</option>';
                        echo '<option value="rescue in progress" ' . ($row['status'] == 'rescue in progress' ? 'selected' : '') . '>Rescue in Progress</option>';
                        echo '</select>';
                        echo '</div>';
                        echo '<button class="save-status-button" data-report-id="' . htmlspecialchars($row['id']) . '">Save</button>';
                        echo '</div>';

                        // Responsibility Claim Section
                        echo '<div class="responsibility-section">';
                        echo '<label for="responsibility-' . htmlspecialchars($row['id']) . '">Claim Responsibility:</label>';
                        echo '<select id="responsibility-' . htmlspecialchars($row['id']) . '" class="responsibility-select" data-report-id="' . htmlspecialchars($row['id']) . '">';
                        echo '<option value="">Select Your Entity</option>';
                        echo '<option value="volunteer-' . htmlspecialchars($_SESSION['volunteer_name']) . '">' . htmlspecialchars($_SESSION['volunteer_name']) . ' Volunteer</option>';
                        echo '<option value="shelter-' . htmlspecialchars($_SESSION['shelter_name']) . '">' . htmlspecialchars($_SESSION['shelter_name']) . ' Animal Shelter</option>';
                        echo '</select>';
                        // Input fields for additional details
                        echo '<input type="text" id="volunteer-info-' . htmlspecialchars($row['id']) . '" class="additional-info-input" data-entity-type="volunteer" style="display:none;" placeholder="Enter volunteer details">';
                        echo '<input type="text" id="shelter-info-' . htmlspecialchars($row['id']) . '" class="additional-info-input" data-entity-type="shelter" style="display:none;" placeholder="Enter shelter details">';

                        echo '<button class="save-responsibility-button" data-report-id="' . htmlspecialchars($row['id']) . '">Claim Responsibility</button>';

                        // Display current assigned entity if exists
                        if ($row['responsible_entity']) {
                            echo '<p class="assigned-entity">Currently claimed by: ' . htmlspecialchars($row['responsible_entity']) . '</p>';
                        }

                        echo '</div>'; // Responsibility section end
                        echo '</div>';
                    }
                } else {
                    echo '<p>No reports available.</p>';
                }

                $conn->close();
                ?>
            </div>
           

            <!-- Message Container -->
            <div id="message-container" class="message-container">
                <div id="message-content" class="message-content"></div>
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
 
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    document.querySelector('#reportForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('ReportStray.php', {
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
 
    // Status update handling
    document.querySelectorAll('.save-status-button').forEach(button => {
        button.addEventListener('click', function() {
            const reportId = this.getAttribute('data-report-id');
            const statusSelect = this.previousElementSibling.querySelector('.status-select');
            const status = statusSelect.value;

            if (status && reportId) {
                updateStatus(reportId, status);
            }
        });
    });



    function updateStatus(reportId, status) {
        fetch('ReportStray.php', {
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

function searchShelters() {
    alert('Searching for shelters...');
}

    



        function scrollToFormTitle() {
    document.getElementById('formTitle').scrollIntoView({ behavior: 'smooth' });
}



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
        xhr.open('POST', 'claimReport.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                showMessage('success', 'Responsibility claimed successfully!');
                setTimeout(() => {
                    location.reload(); // Refresh after message disappears
                }, 3000); // Delay refresh to allow the success message to be visible
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
