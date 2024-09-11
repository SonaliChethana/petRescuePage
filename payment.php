<?php
session_start();
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'utils/connect.php'; // Adjust this to your actual connection file

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $card_type = $_POST['card-type'] ?? null;
    $card_name = $_POST['card-name'] ?? null;
    $card_number = $_POST['card-number'] ?? null;
    $expiry_date = $_POST['expiry-date'] ?? null;
    $cvv = $_POST['cvv'] ?? null;
    $billing_address = $_POST['billing-address'] ?? null;
    $zip_code = $_POST['zip-code'] ?? null;

    // Validate all fields
    if ($card_type && $card_name && $card_number && $expiry_date && $cvv && $billing_address && $zip_code) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO payment_details (card_type, card_name, card_number, expiry_date, cvv, billing_address, zip_code) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            // Bind the parameters
            $stmt->bind_param("sssssss", $card_type, $card_name, $card_number, $expiry_date, $cvv, $billing_address, $zip_code);

                      // Execute the statement
if ($stmt->execute()) {
    // Redirect with success message in URL
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit();
} else {
    // Error message if execution fails
    echo "<script>
        handleFormSubmission('error', 'Failed to save payment details: " . $stmt->error . "');
    </script>";
}
        }}


    // Close the connection
    $conn->close();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



    <style>


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

/* General Payment Page Styles */
.payment-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    font-family: 'Arial', sans-serif;
}

.payment-form {
    display: flex;
    flex-direction: column;
}

.payment-form label {
    margin: 10px 0 5px;
    font-weight: bold;
    color: #333;
}

.payment-form input,
.payment-form select {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100%;
    font-size: 16px;
    box-sizing: border-box;
}

.expiry-cvv-container {
    display: flex;
    justify-content: space-between;
}

.expiry-cvv-container div {
    flex: 1;
    margin-right: 10px;
}

.expiry-cvv-container div:last-child {
    margin-right: 0;
}

/* Advanced Card Type Styling */
#card-type {
    appearance: none;
    background: url('images/card-icon.png') no-repeat right center;
    background-size: 20px;
    padding-right: 35px;
}

/* Submit Button Styling */
.payment-submit {
    padding: 15px 20px;
    background-color: #ff9800;
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.payment-submit:hover {
    background-color: #e68900;
}

/* Responsive Styling */
@media (max-width: 600px) {
    .expiry-cvv-container {
        flex-direction: column;
    }

    .expiry-cvv-container div {
        margin-right: 0;
        margin-bottom: 15px;
    }

    .expiry-cvv-container div:last-child {
        margin-bottom: 0;
    }
}

/* Success Message Styles */
.success-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px 40px;
    background-color: rgba(255, 255, 255, 0.9);
    border: 2px solid #8B4513; /* Adjust color as needed */
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    font-size: 24px;
    font-weight: bold;
    color: #8B4513; /* Adjust text color as needed */
    display: none;
    z-index: 1000;
}

/* Optional Animation */
.success-popup.fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
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

    

    <div class="payment-container">
        <h2>Enter Payment Details</h2>
        <form class="payment-form" id="payment-form" action="payment.php" method="post">
            <!-- Card Type Selection -->
            <label for="card-type">Card Type:</label>
            <select id="card-type" name="card-type" required>
                <option value="" disabled selected>Select your card type</option>
                <option value="visa">Visa</option>
                <option value="mastercard">MasterCard</option>
                <option value="amex">American Express</option>
                <option value="discover">Discover</option>
            </select>

            <!-- Name on Card -->
            <label for="card-name">Name on Card:</label>
            <input type="text" id="card-name" name="card-name" required>

            <!-- Card Number -->
            <label for="card-number">Card Number:</label>
            <input type="text" id="card-number" name="card-number" required pattern="\d{13,16}" placeholder="1234 5678 9012 3456">

            <!-- Expiry Date -->
            <div class="expiry-cvv-container">
                <div>
                    <label for="expiry-date">Expiry Date:</label>
                    <input type="text" id="expiry-date" name="expiry-date" required pattern="\d{2}/\d{2}" placeholder="MM/YY">
                </div>

                <!-- CVV -->
                <div>
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" required pattern="\d{3,4}" placeholder="123">
                </div>
            </div>

            <!-- Billing Address -->
            <label for="billing-address">Billing Address:</label>
            <input type="text" id="billing-address" name="billing-address" required placeholder="123 Main St">

            <!-- Zip Code -->
            <label for="zip-code">Zip Code:</label>
            <input type="text" id="zip-code" name="zip-code" required pattern="\d{5}" placeholder="12345">

            <!-- Submit Button -->
            <button type="submit" class="payment-submit" id="donateNowButton">Donate Now</button>

        </form>
    </div>

    <div id="successMessage" class="success-popup">Your donation was successful!</div>




    
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
    // Check if the URL contains the success flag
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        // Display the success message
        const successMessage = document.getElementById('successMessage');
        successMessage.style.display = 'block';
        successMessage.classList.add('fade-in');
        
        // Hide the message after a few seconds
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000); // Adjust the timeout duration as needed
    }
});

</script>


</body>
</html>
