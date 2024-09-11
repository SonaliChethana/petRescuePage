
<?php
session_start();
require_once 'utils/connect.php';  // Ensure this path is correct
// Fetch vet clinic profiles from the database
$query = "SELECT * FROM vet_clinics";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vet clinics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>

        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

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
    margin-right: 20px; /* Reduced space between logo and title */

}

.logo img {
    width: 100% !important; /* Adjusted logo size */
    height: 100px !important;
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


/* Media Queries for Responsive Design */
@media (max-width: 1024px) {
    .header {
        flex-direction: column;
        height: auto;
        padding: 20px;
        text-align: center;
    }

    .logo {
        margin-bottom: 15px;
        justify-content: center;
    }

    .title {
        margin-right: 0;
        margin-bottom: 20px;
        font-size: 2em;
    }

    .nav {
        flex-direction: column;
        width: 100%;
    }

    .nav-list {
        justify-content: center;
        margin-right: 0;
    }

    .nav-item {
        margin-left: 0;
        margin-bottom: 10px;
    }

    .nav-link {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 768px) {
    .title {
        font-size: 1.8em;
    }

    .nav-link {
        padding: 8px 15px;
    }
}

@media (max-width: 480px) {
    .logo img {
        height: 80px !important;
    }

    .title {
        font-size: 1.5em;
    }

    .nav-link {
        padding: 6px 10px;
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

 /* Advanced and unique styles for the Vet Section */
.vet-section {
    padding: 50px;
    background: radial-gradient(circle, #e3f2fd, #bbdefb); /* Soft blue gradient */
    border-radius: 30px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    margin: 50px 0;
    text-align: center;
}


.vet-text {
    margin-bottom: 40px;
}

.vet-text h2 {
    font-size: 2.5em;
    font-weight: 800;
    color: #0d47a1; /* Deep blue color */
    margin-bottom: 20px;
    line-height: 1.4;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.vet-text p {
    font-size: 1.375em;
    color: #0d47a1; /* Matching text color */
    line-height: 1.6;
    margin-bottom: 0;
}

.vet-services {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
}

.service-item {
    flex: 1 1 250px;
    max-width: 100%;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    box-sizing: border-box;

}

.service-icon {
    width: 80px;
    height: 80px;
    margin-bottom: 15px;
    display: block;
    margin: 0 auto;
}

.service-item h3 {
    font-size: 1.5em;
    color: #0d47a1;
    margin-bottom: 10px;
}

.service-item p {
    font-size: 1.125em;
    color: #0d47a1;
}

/* Media queries for responsive design */
@media (max-width: 768px) {
    .vet-section {
        padding: 30px;
    }

    .vet-text h2 {
        font-size: 2em;
    }

    .vet-text p {
        font-size: 1.2em;
    }

    .service-item {
        flex: 1 1 100%;
        margin-bottom: 20px;
    }

    .service-icon {
        width: 70px;
        height: 70px;
    }

    .service-item h3 {
        font-size: 1.375em;
    }

    .service-item p {
        font-size: 1em;
    }
}

@media (max-width: 480px) {
    .vet-section {
        padding: 20px;
    }

    .vet-text h2 {
        font-size: 1.5em;
    }

    .vet-text p {
        font-size: 1em;
    }

    .service-icon {
        width: 60px;
        height: 60px;
    }

    .service-item h3 {
        font-size: 1.2em;
    }

    .service-item p {
        font-size: 0.9em;
    }
}


.container {
    width: 90%;
    margin: 0 auto;
    padding: 20px;
}

/* Vet Clinics Section */
.vet-clinics {
    background-color: #f9f9f9;
    padding: 40px 0;
}

.clinic-profile {
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
    <div class="vet-section">
    <div class="vet-text">
        <h2>Expert Care for Your Pets</h2>
        <p>Our network of veterinary clinics offers comprehensive care to ensure your pets receive the best treatment possible. From vaccinations to specialized care, find the support your pets need to stay healthy and happy.</p>
    </div>
    <div class="vet-services">
        <div class="service-item">
            <img src="images/vaccine-icon.png" alt="Vaccination" class="service-icon">
            <h3>Vaccinations</h3>
            <p>Keep your pets up-to-date with essential vaccinations.</p>
        </div>
        <div class="service-item">
            <img src="images/clinic-icon.png" alt="General Care" class="service-icon">
            <h3>General Care</h3>
            <p>Routine check-ups and preventive care for overall health.</p>
        </div>
        <div class="service-item">
            <img src="images/emergency-icon.png" alt="Emergency Care" class="service-icon">
            <h3>Emergency Care</h3>
            <p>24/7 emergency services for urgent situations.</p>
        </div>
    </div>
</div>


<div class="vet-clinics">
    <div class="container">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="clinic-profile">
                <div class="profile-container">
                    <div class="profile-header">
                        <img src="<?php echo htmlspecialchars($row['logo'] ?? 'default-logo.png'); ?>" alt="Clinic Logo">
                        <div>
                            <h1><?php echo htmlspecialchars($row['clinic_name']); ?></h1>
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
                                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="Clinic Photo">
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


        document.addEventListener('DOMContentLoaded', () => {
            fetch('fetch_vet_clinics.php')
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('vet-clinics-list');
                    data.forEach(clinic => {
                        const clinicDiv = document.createElement('div');
                        clinicDiv.className = 'vet-clinic';
                        clinicDiv.innerHTML = `
                            <img src="${clinic.profile_image}" alt="${clinic.name}" class="clinic-image">
                            <h2>${clinic.name}</h2>
                            <p>${clinic.address}</p>
                            <a href="vet_clinic_profile.html?id=${clinic.id}" class="learn-more-button">Learn More</a>
                        `;
                        list.appendChild(clinicDiv);
                    });
                });
        });

        document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const clinicProfiles = document.querySelectorAll('.clinic-profile');

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        clinicProfiles.forEach(profile => {
            const name = profile.querySelector('.clinic-name').textContent.toLowerCase();
            if (name.includes(query)) {
                profile.style.display = '';
            } else {
                profile.style.display = 'none';
            }
        });
    });
});

    </script>
</body>
</html>
