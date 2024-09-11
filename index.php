<!doctype html>
<html class="no-js" lang="en">

<head>
    <!-- meta data -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--font-family-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&amp;subset=devanagari,latin-ext" rel="stylesheet">
    <title>StraySaver</title>
    <link rel="stylesheet" href="css/home.css">
    <!-- Include Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    

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
                    <li class="nav-item"><a href="registration.php" class="nav-link signup-button">Sign Up</a></li>
                    <li class="nav-item"><a href="login.php" class="nav-link login-button">Log In</a></li>
                    <li class="nav-item emergency-container">
                        <a href="Emergency.php?from=homepage" class="emergency-nav-button">
                            <span class="icon">⚠️</span> <!-- Changed to a yellow warning icon -->
                            <span class="emergency-text">Emergency Rescue</span>    
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- Integrating Welcome Hero Content -->
        <div class="welcome-hero-content">
            <h2>Welcome to <br>StraySaver</h2>
            <p>Join us in making a difference in the lives of stray dogs through rescue, rehabilitation, and rehoming.</p>
            <a href="registration.php" class="signup-button">Join StraySaver!</a>
        </div>
    </header>

    <section id="about-us-section" class="about">
    <div class="container">
        <div class="row about-content">
            <!-- Our Mission Card -->
            <div class="col-md-6">
                <div class="about-card mission-card">
                    <div class="card-content">
                        <div class="card-icon mission-icon">
                            <img src="images/mission-icon.gif" alt="Mission Icon" class="icon-image">
                        </div>
                        <h3 class="card-title">Our Mission</h3>
                        <p class="card-description">To rescue, rehabilitate, and rehome stray dogs while raising awareness about responsible pet ownership.</p>
                    </div>
                </div>
            </div>
            <!-- Our Vision Card -->
            <div class="col-md-6">
                <div class="about-card vision-card">
                    <div class="card-content">
                        <div class="card-icon vision-icon">
                            <img src="images/vision-icon.jpg" alt="Vision Icon" class="icon-image">
                        </div>
                        <h3 class="card-title">Our Vision</h3>
                        <p class="card-description">A world where every stray dog finds a loving home and every community embraces the importance of animal welfare.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>






<section id="services-section" class="services-section layout-padding">
    <div class="container">
        <div class="heading-container">
            <h2 class="section-title">Our Services</h2>
            <p class="section-description">
                Our web application is dedicated to rescuing street dogs and minimizing their impact on the community. With features designed to streamline the adoption process, facilitate reporting of lost dogs, and connect volunteers with rescue efforts, we aim to create a compassionate community platform. By leveraging technology, we empower users to make a meaningful difference in the lives of stray dogs, promoting responsible pet ownership and fostering a more caring environment for both animals and people.
            </p>
        </div>
        <div class="slider-container">
            <div class="slider">
                <div class="slider-content">
                    <div class="service-box">
                        <div class="img-box">
                            <img src="images/StrayIcon.jpg" alt="Report Stray Dogs" />
                        </div>
                        <div class="detail-box">
                            <h5>Report<br>Stray Dogs</h5>
                            <p>Help a dog in need.</p>
                        </div>
                    </div>
                    <div class="service-box">
                        <div class="img-box">
                            <img src="images/iconEmergency.jpg" alt="Emergency Report" />
                        </div>
                        <div class="detail-box">
                            <h5>Emergency<br>Report</h5>
                            <p>Immediate help for a dog in danger.</p>
                        </div>
                    </div>
                    <div class="service-box">
                        <div class="img-box">
                            <img src="images/lost-icon.png" alt="Reunite Paws" />
                        </div>
                        <div class="detail-box">
                            <h5>Reunite<br>Paws</h5>
                            <p>Help a lost dog find its way home.</p>
                        </div>
                    </div>
                    <div class="service-box">
                        <div class="img-box">
                            <img src="images/volunteers.png" alt="Volunteer Opportunities" />
                        </div>
                        <div class="detail-box">
                            <h5>Volunteer<br>Opportunities</h5>
                            <p>Join us to help a dog in need.</p>
                        </div>
                    </div>
                    <div class="service-box">
                        <div class="img-box">
                            <img src="images/AdoptIcon.jpg" alt="Adoption Process" />
                        </div>
                        <div class="detail-box">
                            <h5>Adoption<br>Process</h5>
                            <p>Give a dog a forever home.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="emergency-section">
    <h2>Emergency Help</h2>
    <p>If you encounter a stray dog in need of urgent care or find yourself in a situation requiring immediate assistance for a pet, please click the emergency button below. Your report will be processed as a priority.</p>
    <a href="Emergency.php" class="emergency-button">
        <span class="emergency-icon">🚨</span>
        Emergency Help
    </a>
</div>








    <!-- service section ends -->

    <section id="strayserver-section" class="strayserver">
    <div class="strayserver-details">
        <div class="section-heading text-center">
            <h2>StraySaver</h2>
            <p>Helping to rescue street dogs and provide them a better life.</p>
        </div>
        <div class="container">
            <div class="strayserver-content">
                <div class="isotope">
                    <div class="item">
                        <img src="images/rescue.jpg" alt="Rescue Operation" />
                        <div class="isotope-overlay">
                            <a href="#">Rescue Operation</a>
                        </div>
                    </div>
                    <div class="item">
                        <img src="images/Dogshelter.jpg" alt="Dog Shelter" />
                        <div class="isotope-overlay">
                            <a href="#">Dog Shelter</a>
                        </div>
                    </div>
                    <div class="item">
                        <img src="images/Dogcare.jpg" alt="Medical Care" />
                        <div class="isotope-overlay">
                            <a href="#">Medical Care</a>
                        </div>
                    </div>
                    <div class="item">
                        <img src="images/Adoptdod.jpeg" alt="Adopt a Dog" />
                        <div class="isotope-overlay">
                            <a href="#">Adopt a Dog</a>
                        </div>
                    </div>
                    <div class="item">
                        <img src="images/lostpet.jpg" alt="Lost Pets" />
                        <div class="isotope-overlay">
                            <a href="#">Lost Pets</a>
                        </div>
                    </div>
                    <div class="item">
                        <img src="images/volunteer.jpg" alt="Volunteer" />
                        <div class="isotope-overlay">
                            <a href="#">Volunteer</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    

<section id="contact-us-section" class="contact-us">
    <div class="container">
        <div class="section-heading text-center">
            <h2>Contact Us</h2>
            <p>Drop us a message and we’ll get back to you shortly.</p>
        </div>
        <div class="contact-form">
            <form action="submit_contact.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Your Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>
    </div>
</section>





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
    const slider = document.querySelector('.slider-content');
    const slides = document.querySelectorAll('.box');
    const totalSlides = slides.length;
    let index = 0;

    function showSlide() {
        index = (index + 1) % totalSlides; // Wrap around to the first slide
        slider.style.transform = `translateX(-${index * 100}%)`;
        updatePagination();
    }

    function updatePagination() {
        const paginationDots = document.querySelectorAll('.slider-pagination span');
        paginationDots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
    }

    setInterval(showSlide, 3000); // Change slide every 3 seconds

    // Create pagination dots
    const pagination = document.createElement('div');
    pagination.className = 'slider-pagination';
    slides.forEach((_, i) => {
        const dot = document.createElement('span');
        dot.addEventListener('click', () => {
            index = i;
            showSlide();
        });
        pagination.appendChild(dot);
    });
    document.querySelector('.slider-container').appendChild(pagination);
    updatePagination(); 
});


</script>




</body>

</html>
