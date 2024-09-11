<?php
session_start();
require_once 'utils/connect.php';

// Initialize response variables
$response = [
    'success' => false,
    'message' => 'Failed to save details'
];

//fetch animal shelter profile from the database
// Fetch vet clinic profiles from the database
$query = "SELECT * FROM animal_shelters";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['name'], $_POST['type'], $_POST['contactPerson'], $_POST['phoneNumber'], $_POST['email'], $_POST['address'], $_POST['geolocation'], $_POST['capacity'], $_POST['availability'])) {
        $name = $_POST['name'];
        $type = $_POST['type'];
        $contactPerson = $_POST['contactPerson'];
        $phoneNumber = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $geolocation = $_POST['geolocation'];
        $capacity = $_POST['capacity'];
        $availability = $_POST['availability'];

        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO shelterfoster (name, type, contact_person, phone_number, email, address, geolocation, capacity, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssis", $name, $type, $contactPerson, $phoneNumber, $email, $address, $geolocation, $capacity, $availability);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Details successfully saved!';
        } else {
            $response['message'] = 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = 'Please fill all required fields';
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
    <title>Register Your Shelter or Foster Home</title>
    <link rel="stylesheet" href="css/shelterfoster.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            width: 100% !important; /* Adjusted logo size */
            margin-right: 15px; /* Space between logo image and text */
            border-radius: 50%; /* Circular shape for a friendly look */
            border: 3px ; /* Border around the logo */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Enhanced shadow for depth */
            height: 100px !important;
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


/* Responsive adjustments */
@media (max-width: 1200px) {
    .header {
        height: auto; /* Adjust header height for smaller screens */
        padding: 10px; /* Add some padding for better spacing */
    }

    .logo {
        font-size: 1em; /* Reduce font size for smaller screens */
        margin-right: 20px; /* Reduce space between logo and title */
    }

    .logo img {
        height: 80px; /* Adjust logo size for smaller screens */
    }

    .title {
        font-size: 2em; /* Reduce title font size */
        margin-right: 50px; /* Reduce space between title and nav */
    }

    .nav {
        margin-left: 0; /* Remove margin to center align nav */
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        align-items: center; /* Center-align nav items */
    }

    .nav-item {
        margin-left: 0; /* Remove left margin for stacked items */
        margin-bottom: 10px; /* Add space between nav items */
    }
}

@media (max-width: 768px) {
    .header {
        flex-direction: column; /* Stack header items vertically */
        align-items: flex-start; /* Align items to the start */
        text-align: center; /* Center-align text */
    }

    .logo {
        margin-right: 0; /* Remove right margin */
        margin-bottom: 10px; /* Add space below the logo */
    }

    .title {
        font-size: 1.5em; /* Further reduce title font size */
        margin-right: 0; /* Remove right margin */
        margin-bottom: 10px; /* Add space below the title */
    }

    .nav {
        margin-left: 0; /* Remove left margin */
        margin-right: 0; /* Remove right margin */
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        width: 100%; /* Full width for nav list */
    }

    .nav-item {
        margin-left: 0; /* Remove left margin */
        margin-bottom: 10px; /* Add space between nav items */
    }
}

@media (max-width: 480px) {
    .logo img {
        height: 60px; /* Further adjust logo size for very small screens */
    }

    .title {
        font-size: 1.2em; /* Reduce title font size further */
    }

    .nav-link {
        padding: 8px 15px; /* Reduce padding for smaller screens */
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

  /* Advanced and unique styles for the Shelter Section */
.shelter-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 50px;
    background: radial-gradient(circle, #fff2e6, #ffcc99); /* Soft, warm gradient */
    border-radius: 30px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2); /* Softer shadow */
    margin: 50px 0;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.shelter-section::before {
    content: "";
    position: absolute;
    top: 20px;
    left: 20px;
    width: 150px;
    height: 150px;
    background: rgba(255, 230, 204, 0.4); /* Light warm overlay */
    border-radius: 50%;
    z-index: 0;
}

.shelter-text {
    flex: 1;
    max-width: 55%;
    position: relative;
    z-index: 1;
}

.shelter-text h2 {
    font-size: 36px;
    font-weight: 800;
    color: #8c6d5c; /* Warm brown color */
    margin-bottom: 20px;
    line-height: 1.4;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2); /* Softer text shadow */
}

.shelter-text p {
    font-size: 22px;
    color: #9e8a78; /* Soft brown color */
    line-height: 1.6;
    margin-bottom: 0;
}

.shelter-icon {
    flex: 0 0 auto;
    width: 180px; /* Slightly reduced size */
    height: 180px; /* Maintain aspect ratio */
    border-radius: 50%;
    border: 6px solid #8c6d5c; /* Warm brown border */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); /* Softer shadow */
    transform: rotate(-10deg); /* Less rotation */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    z-index: 1;
}

.shelter-icon:hover {
    transform: rotate(0deg);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3); /* Enhanced shadow on hover */
}

/* Additional styling for responsiveness */
@media (max-width: 768px) {
    .shelter-section {
        flex-direction: column;
        text-align: center;
    }
    
    .shelter-text {
        max-width: 100%;
        margin-bottom: 20px;
    }
    
    .shelter-icon {
        width: 160px; /* Adjust size for smaller screens */
        height: 160px; /* Maintain aspect ratio */
    }
}


.container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
}

/* Vet Clinics Section */
.animal-shelters {
    background-color: #f9f9f9;
    padding: 40px 0;
}

.shelter-profile {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.profile-container {
    padding: 20px;
}

.profile-header {
    display: flex;
    align-items: center;
    border-bottom: 2px solid #eee;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.profile-header img {
    border-radius: 50%;
    width: 100px;
    height: 100px;
    object-fit: cover;
    margin-right: 20px;
}

.profile-header h1 {
    margin: 0;
    font-size: 1.5em;
    color: #007BFF;
}

.profile-header p {
    margin: 5px 0;
}

.section {
    margin-bottom: 20px;
}

.section h2 {
    font-size: 1.2em;
    color: #333;
    border-bottom: 2px solid #007BFF;
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.gallery img {
    width: 100%;
    max-width: 200px;
    height: auto;
    border-radius: 8px;
}

.video-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.video-container video {
    width: 100%;
    max-width: 300px;
    border-radius: 8px;
}

/* No Data Available */
p:empty::before {
    content: 'No data available.';
    color: #999;
    font-style: italic;
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
    <div class="shelter-section">
    <div class="shelter-text">
        <h2>Support Stray Dog Fostering and Shelters</h2>
        <p>Your contributions and support make a huge difference in the lives of stray dogs. Explore how you can help or find a shelter near you.</p>
    </div>
    <img src="images/sheltericon.jpg" alt="Shelter Icon" class="shelter-icon">

</div>


<div class="animal_shelters">
    <div class="container">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="shelter-profile">
                <div class="profile-container">
                    <div class="profile-header">
                        <img src="<?php echo htmlspecialchars($row['logo'] ?? 'default-logo.png'); ?>" alt="shelter Logo">
                        <div>
                            <h1><?php echo htmlspecialchars($row['shelter_name']); ?></h1>
                            <p><?php echo htmlspecialchars($row['location']); ?></p>
                            <p>Contact: <?php echo htmlspecialchars($row['contact_number']); ?></p>
                            <p><?php echo nl2br

($row['description']); ?></p>
                        </div>
                    </div>

                    <div class="section">
                        <h2>Services Offered</h2>
                        <p><?php echo nl2br(htmlspecialchars($row['services'])); ?></p>
                    </div>

                    <div class="section">
                        <h2>Opening Hours</h2>
                        <p>
                        <?php
                            // Fetch and decode the open_hours JSON field
                            $open_hours = json_decode($row['open_hours'], true);

                            // Check for JSON errors
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                $open_hours = [];  // Set to empty array if JSON is invalid
                            }

                            if (is_array($open_hours) && !empty($open_hours)) {
                                // Ensure 'open' and 'close' fields exist
                                if (isset($open_hours['open']) && isset($open_hours['close'])) {
                                    echo 'Open: ' . htmlspecialchars($open_hours['open']) . '<br>';
                                    echo 'Close: ' . htmlspecialchars($open_hours['close']) . '<br>';
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
                        <?php if (!empty($row['photos']) && is_array(json_decode($row['photos'], true))): ?>
                            <div class="section">
                                <h2>Photos</h2>
                                <div class="gallery">
                                    <?php
                                        $photos = json_decode($row['photos'], true);
                                        foreach ($photos as $photo):
                                    ?>
                                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="shelter Photo">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="section">
                                <h2>Photos</h2>
                                <p>No photos available.</p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($row['videos']) && is_array(json_decode($row['videos'], true))): ?>
                            <div class="section">
                                <h2>Videos</h2>
                                <div class="video-container">
                                    <?php
                                        $videos = json_decode($row['videos'], true);
                                        foreach ($videos as $video):
                                    ?>
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
            </div>
        <?php } ?>
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
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('shelterForm');
            const notification = document.getElementById('notification');
            const typeButtons = document.querySelectorAll('.type-button');
            const typeInput = document.getElementById('type');
            const cancelButton = document.getElementById('cancelButton');

            typeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    typeButtons.forEach(btn => btn.classList.remove('selected'));
                    button.classList.add('selected');
                    typeInput.value = button.getAttribute('data-value');
                });
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(form);

                if (!typeInput.value) {
                    displayMessage('Please select the type of entity.', 'error');
                    return;
                }

                try {
                    const response = await fetch('ShelterFoster.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        displayMessage(data.message, 'success');
                        form.reset(); // Clear the form after successful submission
                    } else {
                        displayMessage(data.message || 'Failed to save details. Please try again.', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    displayMessage('Failed to save details. Please try again.', 'error');
                }
            });

            function displayMessage(message, type) {
                notification.textContent = message;
                notification.className = `notification ${type}`;
                notification.style.display = 'block';

                // Remove the message after 5 seconds
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000);
            }

            cancelButton.addEventListener('click', () => {
                form.reset(); // Clear the form when cancel is clicked
                notification.style.display = 'none'; // Hide notification if cancel is clicked
            });
        });
    </script>
</body>
</html>
