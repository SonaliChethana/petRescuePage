<?php
require_once 'utils/connect.php';  // Ensure this path is correct

$response = [
    'success' => false,
    'message' => 'Failed to save details'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'], $_POST['age'], $_POST['location'], $_POST['experience'])) {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $location = $_POST['location'];
        $experience = $_POST['experience'];

        $stmt = $conn->prepare("INSERT INTO volunteer (name, age, location, experience) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $name, $age, $location, $experience);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Details successfully saved!';
        } else {
            $response['message'] = 'Failed to save details: ' . $stmt->error;
        }

        $stmt->close();
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
    <title>Volunteer Registration</title>
    <link rel="stylesheet" href="css/volunteer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            height: 150px;
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
            max-width: 200px; /* Adjusted logo size */
            margin-right: 15px; /* Space between logo image and text */
            border-radius: 50%; /* Circular shape for a friendly look */
            border: 3px ; /* Border around the logo */
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


/* Media Queries */
@media (max-width: 1024px) {
    /* For tablets and smaller desktops */
    .header {
        height: 120px;
    }

    .logo img {
        max-width: 150px;
        height: 80px;
    }

    .title {
        font-size: 2em;
        margin-right: 50px;
    }

    .nav-link {
        padding: 8px 16px;
        font-size: 0.9em;
    }
}

@media (max-width: 768px) {
    /* For mobile phones */
    .header {
        height: auto; /* Allow height to adjust automatically */
        flex-direction: column;
        text-align: center;
    }

    .logo img {
        max-width: 120px;
        height: 60px;
    }

    .title {
        font-size: 1.8em;
        margin-right: 0;
        margin-bottom: 10px; /* Add space below title */
    }

    .nav {
        margin: 10px 0;
        flex-direction: column;
    }

    .nav-list {
        flex-direction: column;
        align-items: center;
    }

    .nav-item {
        margin-left: 0;
        margin-bottom: 10px; /* Add space between nav items */
    }

    .nav-link {
        padding: 10px 15px;
        font-size: 1em;
    }
}

@media (max-width: 480px) {
    /* For very small screens (phones in portrait mode) */
    .logo img {
        max-width: 100px;
        height: 50px;
    }

    .title {
        font-size: 1.5em;
    }

    .nav-link {
        padding: 8px 12px;
        font-size: 0.9em;
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

        .hero {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 30px 20px;
    background: linear-gradient(120deg, #fff8e1 0%, #ffe0b2 100%);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    text-align: center;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hero-content {
    max-width: 70%;
    margin-bottom: 20px;
    color: #555;
    z-index: 2;
}

.hero-content h2 {
    font-size: 2.8rem;
    margin-bottom: 15px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    background: linear-gradient(45deg, #ff6f00, #fbc02d);
    -webkit-background-clip: text;
    color: transparent;
    text-shadow: 3px 3px 6px rgba(255, 200, 100, 0.5);
    position: relative;
}

.hero-content p {
    font-size: 1.3rem;
    line-height: 1.6;
    color: #666;
    margin-bottom: 20px;
    letter-spacing: 1.2px;
}

.hero-image {
    width: 100%;
    max-width: 800px; /* Further increased size */
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    z-index: 1;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.hero-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
    filter: brightness(85%) saturate(110%);
    transition: filter 0.4s ease, transform 0.4s ease;
    border-radius: 20px;
    transform: scale(1.04); /* Slight initial scale */
}

.hero-image img:hover {
    transform: scale(1.12);
    filter: brightness(100%) saturate(130%);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3); /* Enhanced shadow on hover */
}

.hero-image::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    width: calc(100% + 20px);
    height: calc(100% + 20px);
    border: 5px solid rgba(255, 87, 34, 0.7);
    border-radius: 25px;
    z-index: -1;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.hero-image:hover::before {
    transform: scale(1.1);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

/* Responsive Styles for Hero Section */
@media (max-width: 1024px) {
    .hero {
        padding: 20px 15px; /* Adjust padding for medium screens */
    }

    .hero-content {
        max-width: 80%; /* Increase max-width for better readability */
        margin-bottom: 15px; /* Slightly reduce space below content */
    }

    .hero-content h2 {
        font-size: 2.2rem; /* Slightly smaller font size */
    }

    .hero-content p {
        font-size: 1.2rem; /* Slightly smaller font size */
    }

    .hero-image {
        max-width: 90%; /* Increase width to use more available space */
    }
}

@media (max-width: 768px) {
    .hero {
        padding: 15px 10px; /* Adjust padding for smaller screens */
    }

    .hero-content {
        max-width: 90%; /* Increase max-width for better readability */
        margin-bottom: 10px; /* Reduce space below content */
    }

    .hero-content h2 {
        font-size: 1.8rem; /* Smaller font size */
    }

    .hero-content p {
        font-size: 1.1rem; /* Smaller font size */
    }

    .hero-image {
        max-width: 100%; /* Ensure the image uses full width */
    }
}

@media (max-width: 480px) {
    .hero {
        padding: 10px 5px; /* Minimal padding for very small screens */
    }

    .hero-content {
        max-width: 100%; /* Full width for the content */
        margin-bottom: 5px; /* Minimal space below content */
    }

    .hero-content h2 {
        font-size: 1.5rem; /* Further reduced font size */
    }

    .hero-content p {
        font-size: 1rem; /* Further reduced font size */
    }

    .hero-image {
        max-width: 100%; /* Full width for the image */
    }

    .hero-image img {
        transform: scale(1); /* Reset scale for very small screens */
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
             
        .registration {
    background: linear-gradient(120deg, #fafafa 0%, #ececec 100%);
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}

.registration h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    background: linear-gradient(45deg, #ff7043, #ffb74d);
    -webkit-background-clip: text;
    color: transparent;
}

.registration p {
    font-size: 1.2rem;
    margin-bottom: 30px;
    color: #666;
}

.notification {
    margin-bottom: 20px;
    padding: 10px;
    border-radius: 10px;
    background: #ffcc80;
    color: #fff;
    display: none;
}

.form-group {
    margin-bottom: 25px;
    text-align: left;
}

.form-group label {
    display: block;
    font-size: 1.1rem;
    margin-bottom: 8px;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 1.2px;
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
    color: #333;
    background: #f7f7f7;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group input[type="text"]:focus,
.form-group textarea:focus {
    border-color: #ff7043;
    box-shadow: 0 0 5px rgba(255, 112, 67, 0.5);
    outline: none;
}

textarea {
    resize: vertical;
    height: 120px;
}

button[type="submit"] {
    background: linear-gradient(45deg, #ff7043, #ffb74d);
    color: #fff;
    padding: 12px 25px;
    font-size: 1.2rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

button[type="submit"]:hover {
    background: linear-gradient(45deg, #ff5722, #ff9800);
    transform: scale(1.05);
}

button[type="submit"]:active {
    transform: scale(1);
}

/* Media Queries for Responsiveness */

/* Tablets and Small Desktops */
@media (max-width: 768px) {
    .registration {
        padding: 30px 20px;
    }

    .registration h2 {
        font-size: 2rem;
    }

    .registration p {
        font-size: 1rem;
    }

    .form-group label {
        font-size: 1rem;
    }

    .form-group input[type="text"],
    .form-group textarea {
        padding: 10px 12px;
    }

    button[type="submit"] {
        padding: 10px 20px;
        font-size: 1rem;
    }
}

/* Mobile Devices */
@media (max-width: 480px) {
    .registration {
        padding: 20px 15px;
    }

    .registration h2 {
        font-size: 1.75rem;
    }

    .registration p {
        font-size: 0.9rem;
    }

    .form-group label {
        font-size: 0.9rem;
    }

    .form-group input[type="text"],
    .form-group textarea {
        padding: 8px 10px;
    }

    textarea {
        height: 100px;
    }

    button[type="submit"] {
        padding: 8px 15px;
        font-size: 0.9rem;
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
        <section class="hero">
            <div class="hero-content">
                <h2>Become a Volunteer</h2>
                <p>Help save stray dogs by becoming a volunteer!</p>
            </div>
            <div class="hero-image">
                <img src="images/Volunteer.jpg" alt="Volunteer">
            </div>
        </section>

        <br />
        <br />

        <section class="registration">
            <h2>Volunteer Registration</h2>
            <p>Fill in the form below to join our volunteer team.</p>
            <div id="notification" class="notification"></div>
            <form id="volunteerForm" action="volunteer.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="text" id="age" name="age" placeholder="Enter your age" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="Enter your location" required>
                </div>
                <div class="form-group">
                    <label for="experience">Experience</label>
                    <textarea id="experience" name="experience" placeholder="Describe any relevant experience" required></textarea>
                </div>
                <button type="submit">Submit</button>
            </form>
        </section>

        <section class="banner">
            <img src="images/BannerV.jpg" alt="Banner Image">
        </section>
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

    <script>
        document.getElementById('volunteerForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const form = event.target;
            const formData = new FormData(form);
            const notification = document.getElementById('notification');

            // Send form data via fetch
            fetch('volunteer.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessage(data.message, 'success');
                    form.reset(); // Clear the form after successful submission
                } else {
                    displayMessage(data.message || 'Failed to save details. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayMessage('Failed to save details. Please try again.', 'error');
            });
        });

        function displayMessage(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';

            // Remove the message after 5 seconds
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
