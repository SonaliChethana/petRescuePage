<?php
session_start();
require_once 'utils/connect.php'; // Adjust this to your actual connection file

// Initialize a variable for the success message
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $donor_name = $_POST['donor-name'];
    $donor_email = $_POST['donor-email'];
    $donation_amount = $_POST['donation-amount'];
    $frequency = $_POST['frequency'];
    $additional_info = $_POST['additional-info'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO donations (donor_name, donor_email, donation_amount, frequency, additional_info) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $donor_name, $donor_email, $donation_amount, $frequency, $additional_info);

    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "Donation successfully recorded.";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



    <style>

body {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}


 /* Header */
 .header {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('images/bg-1.jpg') no-repeat center center/cover; /* Gradient overlay with background image */
            color: #fff; /* Text color for readability */
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

/* Base styles remain unchanged */

@media (max-width: 1200px) {
    .logo img {
        max-width: 80px; /* Smaller logo on medium screens */
        height: 80px;
    }

    .title {
        font-size: 2em; /* Adjusted font size */
        margin-right: 50px; /* Adjusted margin */
    }

    .nav {
        margin-right: 10px; /* Adjusted margin */
    }

    .nav-link {
        padding: 8px 16px; /* Smaller padding for nav links */
    }
}

@media (max-width: 992px) {
    .header {
        flex-direction: column; /* Stack items vertically */
        align-items: flex-start; /* Align items to the start */
    }

    .header-container {
        flex-direction: column; /* Stack items vertically */
    }

    .logo {
        margin-right: 0; /* Remove margin for vertical stacking */
    }

    .title {
        margin-right: 0; /* Remove margin for vertical stacking */
        text-align: center; /* Center align the title */
    }

    .nav {
        width: 100%; /* Full width for navigation */
        justify-content: center; /* Center align nav items */
        margin-top: 10px; /* Space between title and nav */
    }

    .nav-list {
        flex-direction: column; /* Stack nav items vertically */
        align-items: center; /* Center align nav items */
    }

    .nav-item {
        margin-left: 0; /* Remove left margin for vertical stack */
        margin-bottom: 10px; /* Space between nav items */
    }
}

@media (max-width: 768px) {
    .logo img {
        max-width: 60px; /* Smaller logo on smaller screens */
        height: 60px;
    }

    .title {
        font-size: 1.5em; /* Adjusted font size */
    }

    .nav-link {
        padding: 6px 12px; /* Smaller padding for nav links */
    }
}

@media (max-width: 576px) {
    .logo {
        font-size: 1em; /* Smaller font size for logo text */
    }

    .title {
        font-size: 1.2em; /* Smaller font size for title */
    }

    .nav-link {
        padding: 4px 8px; /* Smaller padding for nav links */
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

   /* Donation Header Section Enhancements */
.donation-header-section {
    background: linear-gradient(135deg, #b76e79, #8e5a61); /* A warmer gradient */
    color: #fff;
    text-align: center;
    padding: 50px 20px; /* Increased padding for more space */
    border-radius: 12px; /* Slightly more rounded corners */
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); /* Deeper shadow for emphasis */
    position: relative;
    overflow: hidden;
}

.donation-header-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3); /* Semi-transparent overlay */
    mix-blend-mode: overlay; /* Blends overlay with gradient */
}

.donation-header-section h1 {
    font-size: 3rem; /* Larger heading */
    font-weight: 800; /* Bolder text */
    margin-bottom: 15px;
    text-transform: uppercase; /* All caps for a modern look */
    letter-spacing: 2px; /* Spaced-out letters */
    z-index: 1;
    position: relative;
}

.donation-header-section p {
    font-size: 1.3rem; /* Larger paragraph text */
    line-height: 1.8;
    max-width: 750px;
    margin: 0 auto;
    z-index: 1;
    position: relative;
}

/* Donation Content Section Enhancements */
.donation-content-section {
    padding: 70px 30px;
    background: linear-gradient(135deg, #fef9f7 0%, #f7f7f7 100%), 
                radial-gradient(circle at top left, rgba(255, 127, 80, 0.2), transparent 50%), 
                radial-gradient(circle at bottom right, rgba(142, 90, 97, 0.2), transparent 50%);
    background-blend-mode: multiply, screen, overlay;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    margin-top: 40px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.donation-content-section::before {
    content: '';
    position: absolute;
    top: -20px;
    left: -20px;
    right: -20px;
    bottom: -20px;
    background: radial-gradient(circle, rgba(255, 127, 80, 0.15) 20%, transparent 80%), 
                radial-gradient(circle at bottom left, rgba(142, 90, 97, 0.15) 20%, transparent 80%);
    border-radius: 50%;
    z-index: -1;
    opacity: 0.6;
    transform: scale(1.1);
}


.donation-content-section h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #4b3832;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    position: relative;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    background: linear-gradient(to right, #ff7f50, #8e5a61);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-align: center;
}



.donation-methods {
    display: flex;
    flex-wrap: wrap;
    gap: 150px;
    margin-bottom: 50px;
    margin-left: 100px;
    margin-right: 100px;
}

.donation-item {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
    max-width: 350px;
    text-align: center;
    position: relative;
    backdrop-filter: blur(10px);
    overflow: hidden;
}

.donation-item h3 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #8e5a61;
    margin-bottom: 20px;
    position: relative;
}

.donation-item p {
    font-size: 1.2rem;
    color: #555;
    line-height: 1.8;
    margin-bottom: 30px;
    position: relative;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
}

.donate-button {
    display: inline-block;
    padding: 15px 30px;
    background-color: #8e5a61;
    color: #fff;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    transition: background-color 0.3s ease, transform 0.3s ease;
    position: relative;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}

.donate-button:hover {
    background-color: #b76e79;
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
}



/* Centered and Styled List */
.donation-content-section ul {
    list-style: none; /* Remove default bullet points */
    padding: 0; /* Remove default padding */
    margin: 0 auto; /* Center align the list within its container */
    max-width: 800px; /* Set a max width for better control */
    text-align: center; /* Center the text inside the list */
}

.donation-content-section ul li {
    font-size: 1.3rem;
    color: #4b3832;
    padding: 10px 0; /* Vertical padding for spacing */
    line-height: 1.8;
    display: block; /* Ensure list items are stacked vertically */
    text-align: left; /* Align text to the left within the list item */
    position: relative;
    margin-bottom: 15px; /* Space between items */
}



.donation-content-section ul li::before {
    content: "✓";
    color: #ff7f50; /* Coral color for the icon */
    font-size: 1.7rem; /* Size of the icon */
    margin-right: 10px; /* Space between icon and text */
    vertical-align: middle; /* Align icon with text */
}
.donation-content-section ul li:last-child {
    border-bottom: none; /* Remove border from the last item */
}


/* Styles for donation-help-text */
.donation-help-text {
    font-size: 1.3rem; /* Slightly larger text for emphasis */
    color: #4b3832; /* Warm dark brown */
    margin-bottom: 20px; /* Space below the paragraph */
    font-weight: 600; /* Slightly bolder text */
    text-align: center; /* Center align text */
    background: rgba(255, 255, 255, 0.8); /* Light background for contrast */
    border-radius: 6px; /* Rounded corners */
    padding: 10px; /* Padding for better readability */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

/* Styles for why-donate-text */
.why-donate-text {
    font-size: 1.2rem;
    color: #555;
    line-height: 1.8;
    margin-bottom: 30px;
    text-align: center; /* Center align text */
    background: rgba(255, 255, 255, 0.9); /* Slightly opaque background */
    border-radius: 8px; /* Rounded corners */
    padding: 15px; /* Padding for better readability */
    border: 1px solid #ddd; /* Light border for definition */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transitions */
}

.why-donate-text:hover {
    background-color: #f9f9f9; /* Slightly darker background on hover */
    transform: translateY(-3px); /* Lift effect on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
}

/* Styles for contact-text */
.contact-text {
    font-size: 1.2rem;
    color: #555;
    line-height: 1.8;
    margin-bottom: 30px;
    text-align: center; /* Center align text */
    background: rgba(255, 255, 255, 0.9); /* Slightly opaque background */
    border-radius: 8px; /* Rounded corners */
    padding: 15px; /* Padding for better readability */
    border: 1px solid #ddd; /* Light border for definition */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transitions */
}

/* Additional Responsive Design */
@media (max-width: 800px) {
    .donation-methods {
        flex-direction: column;
        align-items: center;
    }

    .donation-item {
        max-width: 100%;
        margin-bottom: 20px;
    }

    .donation-content-section h2 {
        font-size: 2rem;
        margin-bottom: 20px;
    }

    .donation-content-section ul li {
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .donation-content-section {
        padding: 50px 20px;
    }

    .donation-content-section h2 {
        font-size: 1.8rem;
    }

    .donation-methods {
        gap: 20px;
    }

    .donation-item {
        padding: 20px;
    }

    .donate-button {
        padding: 12px 25px;
    }
}




html {
    scroll-behavior: smooth;
}

/* Container styling */
.donation-form-container {
    max-width: 800px;
    margin: auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    border: 1px solid #ddd;
}

/* Heading styling */
.donation-form-container h2 {
    text-align: center;
    color: #333;
    font-family: 'Arial', sans-serif;
    font-size: 2.2rem;
    margin-bottom: 30px;
    font-weight: 700;
}

/* Form styling */
.donation-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.donation-form label {
    font-weight: bold;
    color: #444;
    font-size: 1.1rem;
}
/* Donation Amount Label styling */
.donation-form label[for="donation-amount"] {
    font-weight: bold;
    color: #555;
    margin-bottom: 5px;
}

/* Form elements styling */
.donation-form input[type="text"],
.donation-form input[type="email"],
.donation-form textarea,
.donation-form select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    background-color: #fafafa;
}

.donation-form input[type="text"]:focus,
.donation-form input[type="email"]:focus,
.donation-form textarea:focus,
.donation-form select:focus {
    border-color: #ff8c00; /* Orange color for focus */
    outline: none;
}

/* Donation Amount Container styling */
.donation-amount-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.donation-amount-container input[type="text"] {
    flex: 1;
    font-size: 1rem;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fafafa;

}

.donation-amount-container #currency {
    font-size: 1rem;
    color: #444;
    background-color: #eee;
    padding: 10px;
    border-radius: 5px;
}

/* Predefined Amounts styling */
.predefined-amounts {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 10px;
}

.amount-button {
    background-color: #ff8c00;
    color: #fff;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.amount-button:hover {
    background-color: #e07b00; /* Darker orange on hover */
}

/* Frequency fieldset styling */
.donation-frequency {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    background-color: #fafafa;
}

.donation-frequency legend {
    font-weight: bold;
    color: #555;
    font-size: 1.1rem;
}

.donation-frequency label {
    display: block;
    margin-bottom: 12px;
    font-size: 1rem;
    color: #444;
}

/* Additional Information textarea styling */
.donation-form textarea {
    resize: vertical;
    font-size: 1rem;
}

/* Submit button styling */
.donation-submit {
    background-color: #ff8c00;
    color: #fff;
    padding: 15px;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.donation-submit:hover {
    background-color: #e07b00; /* Darker orange on hover */
}

/* Responsive styling */
@media (max-width: 768px) {
    .donation-form-container {
        padding: 20px;
    }

    .donation-amount-container {
        flex-direction: column;
    }

    .donation-amount-container input[type="text"],
    .donation-amount-container #currency {
        width: 100%;
    }

    .predefined-amounts {
        gap: 10px;
    }

    .donation-frequency {
        padding: 10px;
    }

    .donation-submit {
        padding: 12px;
        font-size: 1rem;
    }
}


/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5); /* Darker background for focus */
    padding-top: 60px;
    font-family: 'Arial', sans-serif;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 30px;
    border: 2px solid #888;
    width: 90%; /* Changed to 90% for better fit on smaller screens */
    max-width: 400px;
    text-align: center;
    border-radius: 10px;
    position: relative;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    background: linear-gradient(135deg, #f2f2f2, #ffffff);
}

/* Success and Error Colors */
.modal-content.success {
    border-color: #4CAF50; /* Green border for success */
    background: linear-gradient(135deg, #d4edda, #c3e6cb); /* Light green background */
}

.modal-content.error {
    border-color: #f44336; /* Red border for error */
    background: linear-gradient(135deg, #f8d7da, #f5c6cb); /* Light red background */
}

.close {
    color: #aaa;
    float: right;
    font-size: 24px;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 20px;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

#popupMessage {
    font-size: 18px;
    margin-top: 20px;
    color: #333;
}

#popupMessage.success {
    color: #155724; /* Dark green text for success */
}

#popupMessage.error {
    color: #721c24; /* Dark red text for error */
}

/* Icon with Larger Size */
.dog-icon {
    width: 200px; /* Increased size */
    height: 200px;
    margin-bottom: 5px;
    border-radius: 50%;
}

/* Button Styles */
.modal-content .modal-button {
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #ff9800;
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.modal-content .modal-button:hover {
    background-color: #e68900;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .modal-content {
        padding: 20px; /* Reduced padding for smaller screens */
        width: 95%; /* Adjusted width for better fit */
        max-width: 350px; /* Reduced max-width for smaller screens */
    }

    .close {
        font-size: 20px; /* Smaller close button for smaller screens */
        right: 15px;
    }

    #popupMessage {
        font-size: 16px; /* Smaller font size for better readability */
        margin-top: 15px;
    }

    /* Larger Icon for Smaller Screens */
    .dog-icon {
        width: 70px;
        height: 70px;
        margin-bottom: 15px;
    }

    .modal-content .modal-button {
        padding: 8px 16px; /* Adjusted padding for smaller screens */
        font-size: 14px; /* Reduced font size for smaller screens */
    }
}

@media (max-width: 480px) {
    .modal-content {
        padding: 15px; /* Further reduced padding for very small screens */
        width: 100%; /* Full width on very small screens */
        max-width: 300px; /* Smaller max-width */
    }

    .close {
        font-size: 18px; /* Smaller close button */
        right: 10px;
    }

    #popupMessage {
        font-size: 14px; /* Smaller font size */
        margin-top: 10px;
    }

    /* Even Larger Icon for Very Small Screens */
    .dog-icon {
        width: 60px;
        height: 60px; 
        margin-bottom: 10px;
    }

    .modal-content .modal-button {
        padding: 6px 12px; /* Smaller padding */
        font-size: 12px; /* Smaller font size */
    }
}



/* In-Kind Donations Styles */
.inkind-donations {
    padding: 40px;
    background: linear-gradient(135deg, #fef9f7 0%, #f7f7f7 100%), 
    radial-gradient(circle at top left, rgba(255, 127, 80, 0.2), transparent 50%), 
    radial-gradient(circle at bottom right, rgba(142, 90, 97, 0.2), transparent 50%);
    background-blend-mode: multiply, screen, overlay;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin: 20px auto;
    max-width: 1200px;
    font-family: 'Roboto', sans-serif;
}
.imkind-donations::before{
    content: '';
    position: absolute;
    top: -20px;
    left: -20px;
    right: -20px;
    bottom: -20px;
    background: radial-gradient(circle, rgba(255, 127, 80, 0.15) 20%, transparent 80%), 
                radial-gradient(circle at bottom left, rgba(142, 90, 97, 0.15) 20%, transparent 80%);
    border-radius: 50%;
    z-index: -1;
    opacity: 0.6;
    transform: scale(1.1);
}

.inkind-donations h2 {
    font-size: 2.5rem;
    color: #4b3832;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    background: linear-gradient(to right, #ff7f50, #8e5a61);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-transform: uppercase;
}

.inkind-donations p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 20px;
    text-align: center;
    line-height: 1.6;
}

.inkind-items {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    margin: 0 auto;
    max-width: 1200px;
}
.inkind-item {
    flex: 1 1 calc(25% - 20px); /* Four items per row */
    box-sizing: border-box;

    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
}

.inkind-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    background-color: #f0f0f0;
}
.inkind-items .inkind-item:nth-last-child(-n+3) {
    flex: 1 1 calc(33.33% - 20px); /* Three items centered in the lower row */
    margin-left: auto;
    margin-right: auto;
}

.inkind-icon {
    width: 60px;
    height: 60px;
    margin-bottom: 15px;
    object-fit: cover;
}

.inkind-item h4 {
    font-size: 1.5rem;
    color: #8e5a61;
    margin-bottom: 10px;
    font-weight: bold;
    text-transform: capitalize;
}

.inkind-item p {
    font-size: 0.95rem;
    color: #666;
    line-height: 1.6;
    margin-top: auto;
}

.contact-info {
    list-style: none;
    padding: 0;
    margin: 20px auto; /* Center the list horizontally */
    display: flex;
    flex-direction: column;
    align-items: center; /* Center align the list items */
    gap: 10px;
}

.contact-info li {
    font-size: 1rem;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center; /* Center align the text and icon together */
}

.contact-info i {
    color: #f57c00;
    margin-right: 15px;
    font-size: 1.2rem;
}

.contact-link {
    color: #8e5a61;
    text-decoration: none;
    font-weight: bold;
}

.contact-link:hover {
    text-decoration: underline;
}

.inkind-donations h3 {
    font-size: 1.75rem;
    color: #8e5a61;
    margin-top: 40px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
}
   /* Responsive Styles */
@media (max-width: 1024px) {
    .inkind-item {
        flex: 1 1 calc(33.33% - 20px); /* Three items per row */
    }

    .inkind-items .inkind-item:nth-last-child(-n+3) {
        flex: 1 1 calc(33.33% - 20px); /* Three items in lower row */
        margin-left: 0;
        margin-right: 0;
    }
}

@media (max-width: 768px) {
    .inkind-item {
        flex: 1 1 calc(50% - 20px); /* Two items per row */
    }

    .inkind-items .inkind-item:nth-last-child(-n+3) {
        flex: 1 1 calc(50% - 20px); /* Two items in lower row */
        margin-left: 0;
        margin-right: 0;
    }

    .inkind-donations h2 {
        font-size: 2rem;
    }

    .inkind-donations h3 {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .inkind-item {
        flex: 1 1 100%; /* One item per row */
    }

    .inkind-items .inkind-item:nth-last-child(-n+3) {
        flex: 1 1 100%; /* One item per row */
    }

    .inkind-donations h2 {
        font-size: 1.75rem;
    }

    .inkind-donations h3 {
        font-size: 1.25rem;
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
    
    <div class="donation-header-section">
        <h1>Support Our Mission</h1>
        <p>Your contributions can make a difference in the lives of countless stray dogs. Whether it's through monetary donations or in-kind contributions, every little bit helps.</p>
    </div>

<div class="donation-content-section">
    <h2>Ways to Donate</h2>
    <div class="donation-methods">
        <div class="donation-item">
            <h3>Monetary Donations</h3>
            <p>Your financial support helps us cover medical expenses, provide food, and improve shelter facilities for stray dogs.</p>
            <a href="#donation-form" class="donate-button">Donate Money</a>
            </div>
        <div class="donation-item">
            <h3>In-Kind Donations</h3>
            <p>We also accept items like dog food, blankets, toys, and other essentials. Your in-kind donations can be dropped off at our designated locations or sent via mail.</p>
            <a href="#inkind-donations" class="donate-button">Donate Items</a>
        </div>
    </div>

    <h2>How Your Donations Help</h2>
<p class="donation-help-text">Your generosity allows us to:</p>
<ul>
    <li>Rescue and rehabilitate stray dogs</li>
    <li>Provide medical care and vaccinations</li>
    <li>Find forever homes through adoption</li>
    <li>Support foster families who take care of dogs in need</li>
</ul>

<h2>Why Donate?</h2>
<p class="why-donate-text">By contributing to our cause, you are directly impacting the lives of stray dogs who need a second chance at life. Together, we can create a community where every dog has a loving home.</p>

<h2>Contact Us</h2>
<p class="contact-text">If you have any questions about donating, or if you’d like to arrange a special donation, please contact us.</p>

</div>

<br />
<br />


        
<div class="donation-form-container">
    <h2>Donate to Our Cause</h2>
     
     
    <form class="donation-form" id="donation-form" action="payment.php" method="POST">
    <!-- Donor Information -->
        <label for="donor-name">Name:</label>
        <input type="text" id="donor-name" name="donor-name" required>

        <label for="donor-email">Email:</label>
        <input type="email" id="donor-email" name="donor-email" required>

        <!-- Donation Amount -->
        <label for="donation-amount">Donation Amount:</label>
        <div class="donation-amount-container">
        <span id="currency">LKR</span>

            <input type="number" id="donation-amount" name="donation-amount" placeholder="Enter amount" required>
        </div>


        <!-- Predefined Donation Amounts -->
        <div class="predefined-amounts">
            <button type="button" class="amount-button" data-amount="100">100</button>
            <button type="button" class="amount-button" data-amount="500">500</button>
            <button type="button" class="amount-button" data-amount="1000">1000</button>
        </div>

        <!-- Frequency -->
        <fieldset class="donation-frequency">
            <legend>Donation Frequency:</legend>
            <label>
                <input type="radio" name="frequency" value="one-time" checked>
                One-time
            </label>
            <label>
                <input type="radio" name="frequency" value="monthly">
                Monthly
            </label>
        </fieldset>

        <!-- Additional Information -->
        <label for="additional-info">Additional Information:</label>
        <textarea id="additional-info" name="additional-info" rows="4"></textarea>

        <!-- Submit Button -->
        <button type="submit" class="donation-submit">Continue</button>
    </form>
</div>



<!-- In-Kind Donations Section -->
<div id="inkind-donations" class="inkind-donations">
    <h2>In-Kind Donations</h2>
    <p>Your in-kind donations play a vital role in supporting the welfare of stray dogs. These items help us provide comfort, care, and necessary supplies to the dogs in our care. Below is a list of items we accept and ways you can contribute:</p>

    <h3>Items We Accept</h3>
<div class="inkind-items">
    <div class="inkind-item">
        <img src="images/dog-food.png" alt="Dog Food Icon" class="inkind-icon">
        <h4>Dog Food</h4>
        <p>Both wet and dry food. We appreciate high-quality, nutritious options to keep our dogs healthy.</p>
    </div>
    <div class="inkind-item">
        <img src="images/blankets.png" alt="Blankets and Towels Icon" class="inkind-icon">
        <h4>Blankets and Towels</h4>
        <p>These are used for bedding and keeping the dogs warm. Clean, gently used items are welcome.</p>
    </div>
    <div class="inkind-item">
        <img src="images/toys.png" alt="Dog Toys Icon" class="inkind-icon">
        <h4>Dog Toys</h4>
        <p>Durable chew toys and interactive toys help keep the dogs entertained and stimulated.</p>
    </div>
    <div class="inkind-item">
        <img src="images/leashes.png" alt="Leashes and Collars Icon" class="inkind-icon">
        <h4>Leashes and Collars</h4>
        <p>All sizes are needed. These help us manage and walk the dogs safely.</p>
    </div>
    <div class="inkind-item">
        <img src="images/grooming.png" alt="Grooming Supplies Icon" class="inkind-icon">
        <h4>Grooming Supplies</h4>
        <p>Brushes, shampoos, and other grooming tools help us maintain the dogs' hygiene and appearance.</p>
    </div>
    <div class="inkind-item">
        <img src="images/medical.png" alt="Medical Supplies Icon" class="inkind-icon">
        <h4>Medical Supplies</h4>
        <p>Bandages, antiseptics, and other first aid supplies are also appreciated to handle minor injuries and medical needs.</p>
    </div>
    <div class="inkind-item">
        <img src="images/crates.png" alt="Crates and Carriers Icon" class="inkind-icon">
        <h4>Crates and Carriers</h4>
        <p>These are used for transporting dogs safely and comfortably.</p>
    </div>
</div>


    <h3>How to Donate</h3>
    <p>You can drop off your donations at our shelter during our regular business hours. If you prefer, we can arrange for a pickup of larger items or if you have a significant amount to donate. Please <a href="#contact-us" class="contact-link">contact us</a> to schedule a pickup.</p>

    <h3>Special Arrangements</h3>
    <p>If you have specific items that are not listed above or if you would like to make a large donation, please <a href="#contact-us" class="contact-link">get in touch with us directly</a>. We are always open to discussing how we can best use your contributions.</p>

    <h3 id="contact-us">Contact Us for Donations</h3>
    <p>For any questions or to arrange a special donation, please reach out to our team:</p>
    <ul class="contact-info">
        <li><i class="fas fa-envelope"></i> <strong>Email:</strong> <a href="mailto:straysaver10@gmail.com">straysaver10@gmail.com</a></li>
        <li><i class="fas fa-phone"></i> <strong>Phone:</strong> <a href="tel:+1234567890">+1 234 567 890</a></li>
        <li><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> No.6/ Dickmens Road, Colombo 6</li>
    </ul>

    <h3>Thank You for Your Support!</h3>
    <p>Your generosity and support make a significant difference in the lives of the stray dogs we care for. Every donation helps us provide better care and improves the chances of finding loving homes for these deserving animals.</p>
</div>



<!-- Modal -->
<div id="popupModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img src="images/thankyou-icon.jpg" alt="Dog Icon" class="dog-icon">
        <p id="popupMessage" class="<?php echo $popup_type; ?>"></p>
        <button class="modal-button" onclick="window.location.href='Donation.php'">OK</button>
    </div>
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
document.querySelectorAll('.amount-button').forEach(button => {
    button.addEventListener('click', function() {
        const amountInput = document.getElementById('donation-amount');
        const amount = this.getAttribute('data-amount');

        // Set the predefined amount and clear the custom flag
        amountInput.value = amount;
        delete amountInput.dataset.custom; // Remove the custom data attribute
    });
});

// Allow user to enter a custom amount
document.getElementById('donation-amount').addEventListener('input', function() {
    this.dataset.custom = true; // Mark input as custom
});



 // Modal handling
 const popupModal = document.getElementById("popupModal");
        const popupMessage = document.getElementById("popupMessage");
        const closeModal = document.getElementsByClassName("close")[0];

        closeModal.onclick = function() {
            popupModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == popupModal) {
                popupModal.style.display = "none";
            }
        }

        // Show the modal if there's a message
        <?php if (!empty($popup_message)): ?>
            popupMessage.textContent = "<?php echo $popup_message; ?>";
            popupModal.classList.add("<?php echo $popup_type; ?>");
            popupModal.style.display = "block";
        <?php endif; ?>

</script>

</body>
</html>
