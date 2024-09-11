

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Saver - Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
  }
      
               
  .header {
  background: linear-gradient(to right, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/puppy.jpg') no-repeat center center;
  background-size: cover;
  color: #fff;
  padding: 0;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  max-width:100%;
}

.header-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 0px;
}

.logo {
  display: flex;
  align-items: center;
  font-family: 'Poppins', sans-serif;
  font-size: 1.5em;
  color: #ff7043;
  text-transform: lowercase;
  letter-spacing: 1px;
  position: relative;
}

.logo img {
  max-width: 90px;
  margin-right: 10px;
  border-radius: 50%;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.logo:hover img {
  transform: rotate(-10deg) scale(1.1);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
}

.title {
  font-family: 'Poppins', sans-serif;
  font-size: 2em;
  color: #f4be77; /* Warm color */
  position: relative;
  display: flex;
  align-items: center;
  text-transform: capitalize;
}

.title::before {
  content: "🐾";
  font-size: 0.8em;
  margin-right: 10px;
  color: #ff7043;
}

.title::after {
  content: "🐕";
  font-size: 0.8em;
  margin-left: 10px;
  color: #ff7043;
  transform: rotate(10deg);
}

.title:hover::before, .title:hover::after {
  transform: scale(1.2) rotate(-10deg);
  transition: transform 0.3s ease;
}

.title:hover {
  color: #d9534f;
}

.nav {
  display: flex;  
  text-align: right;
}

.nav-list {
  display: flex;
  justify-content: flex-end;
  list-style: none;
  margin: 0;
  padding: 0;
  flex-grow: 1;
}

.nav-item {
  margin-left: 20px;
  list-style: none;
}

.nav-link {
  color: #fff;
  text-decoration: none;
  font-weight: bold;
  padding: 10px 20px;
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  transition: background-color 0.3s ease, transform 0.3s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}
.nav-link, .emergency-nav-button {
    display: flex;
    align-items: center; /* Ensures icons and text are vertically aligned */
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: #555;
    transition: color 0.3s ease;
}

.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.4);
  transform: scale(1.05);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.nav-link:active {
  transform: scale(0.95);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.emergency-container {
  display: flex;
  align-items: center;
  
}

.emergency-nav-button {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #ff4c4c;
  padding: 8px 15px;
  border-radius: 30px;
  font-size: 1.2em;
  color: #ffffff;
  text-decoration: none;
  transition: background-color 0.3s ease, transform 0.3s ease;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}

.emergency-nav-button:hover {
  background-color: #ff6666;
  transform: scale(1.05);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.emergency-nav-button:active {
  transform: scale(0.95);
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}

.icon {
  margin-right: 8px;
}

.emergency-text {
  font-weight: bold;
  color: #ffffff;
}

.dashboard-content {
  text-align: center;
  margin-top: -5%;
  padding: 50px 20px;
  position: relative;
  z-index: 5;
}

.dashboard-content h2 {
  font-size: 3.5em;
  font-weight: bold;
  margin: 0;
  color: #fff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}
.nav-link.profile-button,
.nav-link.logout-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px; /* Button width */
    height: 45px; /* Button height */
    border-radius: 50%; /* Circular buttons */
    background: linear-gradient(145deg, #a0522d, #d2691e); /* Gradient from brown to dark orange */
    color: #fff; /* White icon color */
    font-size: 24px; /* Larger icon size */
    padding: 0; /* Remove padding inside the button */
    transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none; /* Remove underline */
    box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.2), -4px -4px 8px rgba(255, 255, 255, 0.1); /* Soft dual shadow effect */
    margin-right: 5px;
}

.nav-link.profile-button:hover,
.nav-link.logout-button:hover {
    background: linear-gradient(145deg, #d2691e, #a0522d); /* Inverted gradient on hover */
    transform: scale(1.1); /* Slightly enlarge on hover */
    box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.3), -6px -6px 12px rgba(255, 255, 255, 0.2); /* Enhanced shadow on hover */
}

.nav-link.profile-button:active,
.nav-link.logout-button:active {
    transform: scale(1); /* Return to original size on click */
    box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.2), -2px -2px 6px rgba(255, 255, 255, 0.1); /* Reduced shadow on click */
}

/* Responsive styles */
@media (max-width: 1200px) {
  .header-container {
    padding: 0 10px; /* Adjust padding */
  }

  .logo {
    font-size: 1.2em; /* Adjust font size */
  }

  .title {
    font-size: 1.6em; /* Adjust font size */
  }

  .title::before, .title::after {
    font-size: 0.7em; /* Adjust emoji size */
  }

  .nav-list {
    flex-direction: column; /* Stack nav items vertically */
    align-items: flex-end; /* Align items to the right */
  }

  .nav-item {
    margin-left: 0; /* Remove left margin */
    margin-bottom: 10px; /* Add space between items */
  }

  .dashboard-content h2 {
    font-size: 2.5em; /* Adjust font size */
  }
}

@media (max-width: 768px) {
  .header {
    height: auto; /* Allow header to adjust height */
    padding: 20px; /* Adjust padding */
  }

  .header-container {
    flex-direction: column;
    align-items: flex-end; /* Align items to the right */
  }

  .logo {
    font-size: 1em; /* Reduce logo font size */
  }

  .title {
    font-size: 1.4em; /* Adjust font size */
  }

  .title::before, .title::after {
    font-size: 0.6em; /* Adjust emoji size */
  }

  .nav-list {
    flex-direction: column; /* Stack nav items vertically */
    align-items: flex-end; /* Align items to the right */
  }

  .nav-item {
    margin-left: 0; /* Remove left margin */
    margin-bottom: 10px; /* Add space between items */
  }

  .emergency-nav-button {
    font-size: 1em; /* Adjust button font size */
    padding: 6px 12px; /* Adjust padding */
  }

  .dashboard-content h2 {
    font-size: 2em; /* Adjust font size */
  }
}

@media (max-width: 480px) {
  .header {
    padding: 15px; /* Adjust padding */
  }

  .header-container {
    padding: 0; /* Remove container padding */
  }

  .logo {
    font-size: 0.9em; /* Reduce logo font size */
  }

  .title {
    font-size: 1.2em; /* Adjust font size */
  }

  .title::before, .title::after {
    font-size: 0.5em; /* Adjust emoji size */
  }

  .nav-list {
    flex-direction: column; /* Stack nav items vertically */
    align-items: flex-end; /* Align items to the right */
  }

  .nav-item {
    margin-bottom: 10px; /* Add space between items */
  }

  .emergency-nav-button {
    font-size: 0.9em; /* Adjust button font size */
    padding: 5px 10px; /* Adjust padding */
  }

  .dashboard-content h2 {
    font-size: 1.5em; /* Adjust font size */
  }
}




        .services-section {
                text-align: center;
                padding: 50px 20px;
                background-color: #f5f5f5;
                max-width: 1400px;
                width: 100%;
                margin: 0 auto; /* Center the section */

            }

            .services-section h2 {
                font-size: 2.5rem;
                color: #333;
                margin-bottom: 30px;
            }

            .services-buttons {
                display: grid;
                grid-template-columns: repeat(6, 1fr); /* 6 columns for desktop */
                justify-content: center;
                gap: 20px;
                width: 100%;
            }

            .services-item {
                background-color: #fff;
                border-radius: 15px;
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                width: 200px;
                text-align: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                padding: 10px; /* Add padding to ensure content fits well */

            }

            .services-item:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            }

            .services-item img {
                width: 100px;
                height: 100px;
                margin: 20px 0;
                transition: transform 0.3s ease;
            }

            .services-item:hover img {
                transform: scale(1.1);
            }

            .services-item h3 {
                font-size: 1.5rem;
                color: #333;
                margin: 15px 0;
            }

            .services-item p {
                color: #666;
                margin-bottom: 20px;
            }

            .services-item a {
                text-decoration: none;
                display: block;
                padding: 20px;
                background-color: #ffa200;
                color: #fff;
                border-radius: 10px;
                transition: background-color 0.3s ease;
            }

            .services-item a:hover {
                background-color: #d0a584;
            }

            .learn-more-button {
              display: inline-block;
              margin-top: 10px;
              padding: 10px 15px;
              background-color: #007bff; /* Blue background */
              color: #fff; /* White text */
              text-decoration: none;
              border-radius: 5px;
              font-size: 14px;
              transition: background-color 0.3s ease;
          }

          .learn-more-button:hover {
              background-color: #0056b3; /* Darker blue on hover */
          }

            /* Responsive styles */
@media (max-width: 1200px) {
    .services-item {
        width: 100%; /* Adjust width for smaller screens */
    }

    .services-item img {
        width: 90px; /* Adjust image size */
        height: 90px;
    }

    .services-item h3 {
        font-size: 1.3rem; /* Adjust font size */
    }
    .services-buttons {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Adjust for smaller screens */
    }
}

@media (max-width: 768px) {
    .services-section {
        padding: 40px 15px; /* Adjust padding */
    }

    
    .services-item img {
        width: 80px; /* Adjust image size */
        height: 80px;
    }

    .services-item h3 {
        font-size: 1.1rem; /* Adjust font size */
    }
}

@media (max-width: 480px) {
    .services-section {
        padding: 30px 10px; /* Adjust padding */
    }

    .services-buttons {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Stack items on mobile */
    }
    .services-item {
        max-width: 300px; /* Set max-width for better fit */
    }

    .services-item img {
        width: 70px; /* Adjust image size */
        height: 70px;
    }

    .services-item h3 {
        font-size: 1rem; /* Adjust font size */
    }

    .services-item a {
        padding: 15px; /* Adjust padding */
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




/* Base styles for the dashboard features */
.dashboard-feature {
    background-color: #f4f1ed; /* Light beige background */
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Deeper shadow for more depth */
}

.feature-header {
    margin-bottom: 1.5rem;
}

.feature-header h2 {
    font-size: 2.25rem;
    color: #6f4e37; /* Rich brown color */
    text-align: center;
    font-family: 'Arial', sans-serif;
    font-weight: bold;
}

.feature-content {
    font-size: 1.125rem;
    color: #5c4033; /* Darker brown color */
    line-height: 1.6;
}

.feature-details {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem; /* Space between items */
}

.detail-item {
    flex: 1 1 calc(50% - 1.5rem); /* Two columns, responsive */
    background-color: #ffffff; /* White background for contrast */
    padding: 1.75rem;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    border: 2px solid #d9bfae; /* Light brown border */
    transition: transform 0.3s, box-shadow 0.3s;
}

.detail-item:hover {
    transform: translateY(-5px); /* Slight lift effect on hover */
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
}

.detail-item h3 {
    font-size: 1.5rem;
    color: #6f4e37; /* Rich brown color */
    margin-bottom: 0.5rem;
}

.detail-item p {
    font-size: 1.125rem;
    color: #6b4f40; /* Medium brown color */
}

.feature-content p {
    margin-top: 1rem;
}

/* Responsive styles */
@media (max-width: 768px) {
    .detail-item {
        flex: 1 1 100%; /* Full width on small screens */
    }
}

@media (max-width: 480px) {
    .feature-header h2 {
        font-size: 1.75rem;
    }

    .feature-content p {
        font-size: 1rem;
    }
}


.link-to-emergency-page {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #ff9900; /* Primary button color */
        border-radius: 5px;
        text-align: center;
        text-decoration: none;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .link-to-emergency-page:hover {
        background-color: #802000; /* Darker shade on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .link-to-emergency-page:active {
        background-color: #802000; /* Even darker shade when clicked */
    }

html {
    scroll-behavior: smooth;
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
                <li class="nav-item emergency-container">
                        <a href="Emergency.php?from=dashboard" class="emergency-nav-button">
                            <span class="icon">⚠️</span> <!-- Changed to a yellow warning icon -->
                            <span class="emergency-text">Emergency Rescue</span>    
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profile.php" class="nav-link profile-button">
                        <i class="fas fa-user-alt"></i> <!-- Use your desired icon here -->
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link logout-button">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </li>
                    
                </ul>
            </nav>
        </div>
        <!-- Integrating Welcome Hero Content -->
        <div class="dashboard-content">
            <h2>In every stray, a story untold; In every click, a life unfolds.❣</h2>

        </div>
    </header>

    <main>
        
        <div class="services-section">
            <h2>Explore Our Services</h2>
            <div class="services-buttons">
                <div class="services-item">
                    <a href="ReportStray.php"><img src="images/speaker.png" alt="Report a Stray"></a>
                    <h3>Report a Stray</h3>
                    <p>Help a dog in need</p>
                    <a href="#report-stray" class="learn-more-button">Learn More</a>

                </div>
                <div class="services-item">
                    <a href="Vetclinic.php"><img src="images/vet.png" alt="Vet Clinic"></a>
                    <h3>Vet Clinics</h3>
                    <p>Immediate assistance</p>
                    <a href="#vet-clinics" class="learn-more-button">Learn More</a>

                </div>
                <div class="services-item">
                <a href="lostfound.php"><img src="images/search.png" alt="Reunite Paws"></a>
                <h3>Reunite Paws</h3>
                    <p>Find lost dogs</p>
                    <a href="#reunite-paws" class="learn-more-button">Learn More</a>

                </div>
                <div class="services-item">
                    <a href="Volunteer.php"><img src="images/volunteer.png" alt="Volunteer"></a>
                    <h3>Volunteer</h3>
                    <p>Join our cause</p>
                    <a href="#volunteer" class="learn-more-button">Learn More</a>

                </div>
                <div class="services-item">
                    <a href="ShelterFoster.php"><img src="images/shelter.png" alt="Adoption"></a>
                    <h3>Shelters/Fosters</h3>
                    <p>Provide a home</p>
                    <a href="#shelters-fosters" class="learn-more-button">Learn More</a>

                </div>
                <div class="services-item">
                    <a href="Donation.php"><img src="images/donate.png" alt="Donate"></a>
                    <h3>Donate</h3>
                    <p>Support our mission</p>
                    <a href="#donation" class="learn-more-button">Learn More</a>

                </div>

            </div>
        </div>



        </div>
        
        <section class="dashboard-feature report-stray-feature">
          <div class="feature-header">
              <h2>Report a Stray</h2>
          </div>
          <div class="feature-content" id="report-stray">
              <p>
                  The "Report a Stray" feature allows users to notify StraySaver about stray dogs in their community. By submitting important details such as location, dog description, and behavior, users help us respond quickly to ensure the safety of the animal and the public.
              </p>
              <div class="feature-details">
                  <div class="detail-item">
                      <h3>📍 Location</h3>
                      <p>Specify the location where the dog was sighted. You can even use your current location for accuracy.</p>
                  </div>
                  <div class="detail-item">
                      <h3>📝 Description</h3>
                      <p>Provide details like the dog's size, color, breed (if known), and any visible injuries.</p>
                  </div>
                  <div class="detail-item">
                      <h3>🐾 Behavior</h3>
                      <p>Describe whether the dog is friendly or aggressive to ensure the correct rescue approach.</p>
                  </div>
                  <div class="detail-item">
                      <h3>📸 Photos</h3>
                      <p>Attach photos to help us better understand the situation.</p>
                  </div>
              </div>
              <p>
                  After submitting the report, StraySaver will investigate and update the status of the rescue. Users and volunteers can also claim responsibility for handling the case.
              </p>
          </div>
      </section>

      <section class="dashboard-feature emergency-rescue-feature">
    <div class="feature-header">
        <h2>Emergency Rescue</h2>
    </div>
    <div class="feature-content" id="emergency-rescue">
        <p>
            The "Emergency Rescue" feature allows users to quickly alert StraySaver to urgent situations involving stray dogs. By providing crucial information such as the emergency type, location, and a detailed description, users enable us to take immediate action to address the situation effectively.
        </p>
        <div class="feature-details">
            <div class="detail-item">
                <h3>🚨 Emergency Type</h3>
                <p>Select the type of emergency, such as severe injury or immediate threat, to prioritize the response accordingly.</p>
            </div>
            <div class="detail-item">
                <h3>📍 Location</h3>
                <p>Provide the exact location of the emergency. You can use your current location or specify an address for accuracy.</p>
            </div>
            <div class="detail-item">
                <h3>📝 Description</h3>
                <p>Describe the situation in detail, including any observations about the dog's condition and the surrounding circumstances.</p>
            </div>
            <div class="detail-item">
                <h3>📸 Photos</h3>
                <p>Upload photos to give us a clearer understanding of the emergency situation and the dog's condition.</p>
            </div>
        </div>
        <p>
            Once you submit an emergency report, StraySaver will initiate a response based on the provided information. Users will receive updates on the status of the rescue, and volunteers can choose to assist with the case.
        </p>
        <a href="Emergency.php?from=dashboard" class="link-to-emergency-page">Go to Emergency Page</a>
    </div>
</section>




      <section class="dashboard-feature vet-clinic-feature">
          <div class="feature-header" id="vet-clinics">
              <h2>Find a Vet Clinic</h2>
          </div>
          <div class="feature-content">
              <p>
                  The "Find a Vet Clinic" feature helps users locate trusted veterinary clinics in their area. Whether you need routine care or emergency services, you can search for clinics based on your location and specific needs.
              </p>
              <div class="feature-details">
                  <div class="detail-item">
                      <h3>📍 Search by Location</h3>
                      <p>Enter your location to find nearby vet clinics. You can also use your current location for more accurate results.</p>
                  </div>
                  <div class="detail-item">
                      <h3>🏥 Specialties</h3>
                      <p>Filter clinics based on the specialties they offer, such as emergency care, surgery, or dermatology.</p>
                  </div>
                  <div class="detail-item">
                      <h3>🔍 Clinic Details</h3>
                      <p>View detailed information about each clinic, including contact information, hours of operation, and services provided.</p>
                  </div>
                  <div class="detail-item">
                      <h3>⭐ Reviews</h3>
                      <p>Read reviews from other pet owners to choose the best clinic for your needs.</p>
                  </div>
              </div>
              <p>
                  To start browsing, click the button below to visit the vet clinic page and find the best care for your pets.
              </p>
          </div>
      </section>


      <section class="dashboard-feature reunite-paws-feature">
          <div class="feature-header" id="reunite-paws">
              <h2>Reunite Paws</h2>
          </div>
          <div class="feature-content">
              <p>
                  The "Reunite Paws" feature is dedicated to helping lost pets find their way back to their owners. By providing essential information and utilizing our services, you can assist us in reuniting pets with their families.
              </p>
              <div class="feature-details">
                  <div class="detail-item">
                      <h3>🔍 Lost and Found Pet Matching</h3>
                      <p>We use advanced algorithms and community reports to match lost pets with found ones, ensuring the quickest reunion.</p>
                  </div>
                  <div class="detail-item">
                      <h3>📣 Community Awareness Campaigns</h3>
                      <p>We run campaigns to spread the word about missing pets and encourage community members to help in the search.</p>
                  </div>
                  <div class="detail-item">
                      <h3>📍 Pet Tracking and Recovery Services</h3>
                      <p>Utilize our tracking services to locate lost pets and provide recovery support to ensure their safe return.</p>
                  </div>
                  <div class="detail-item">
                      <h3>🕒 24/7 Support and Assistance</h3>
                      <p>Our team is available around the clock to provide support and answer any questions related to lost pet recovery.</p>
                  </div>
              </div>
              <p>
                  By engaging with our "Reunite Paws" feature, you contribute to the successful return of pets to their homes and enhance community safety.
              </p>
          </div>
      </section>



      



      <section class="dashboard-feature volunteer-opportunities-feature">
          <div class="feature-header" id="volunteer">
              <h2>Volunteer Opportunities</h2>
          </div>
          <div class="feature-content">
              <p>
                  Join our community and make a difference in the lives of stray animals. There are various ways you can help, whether you have time to volunteer in person or prefer to support us remotely:
              </p>
              <div class="feature-details">
                  <div class="detail-item">
                      <h3>🐕 Rescue Operations</h3>
                      <p>Become part of our rescue teams, helping to save stray animals in need. From emergency response to fostering rescued animals, your contribution can save lives.</p>
                  </div>
                  <div class="detail-item">
                      <h3>👐 Donation Drives</h3>
                      <p>Help us organize donation drives for supplies like food, blankets, and medical care for the stray animals in our care. Whether online or in-person, every bit counts.</p>
                  </div>
                  <div class="detail-item">
                      <h3>📢 Awareness Campaigns</h3>
                      <p>Spread the word! Use your voice and social media platforms to raise awareness about the importance of rescuing stray animals and supporting our cause.</p>
                  </div>
              </div>
              <p>
                  By volunteering with us, you make a tangible impact on the lives of stray animals and contribute to the overall well-being of our community.
              </p>
          </div>
      </section>


      <section class="dashboard-feature shelter-foster-feature">
          <div class="feature-header" id="shelters-fosters">
              <h2>Shelter and Foster Homes</h2>
          </div>
          <div class="feature-content">
              <p>
                  Explore our network of animal shelters and foster homes dedicated to providing care and love to stray and abandoned animals. Whether you're looking to adopt, volunteer, or offer temporary care, this section will help you find the right place to make a difference.
              </p>
              <div class="feature-details">
                  <div class="detail-item">
                      <h3>🌟 Browse Animal Shelters</h3>
                      <p>Find local shelters that provide permanent homes and medical care for animals in need. Each shelter listing includes details about their services, location, and how you can get involved.</p>
                  </div>
                  <div class="detail-item">
                      <h3>🏡 Discover Foster Homes</h3>
                      <p>Explore foster homes where animals receive temporary care and love before finding their forever homes. Learn about the foster care process and how you can support these essential caregivers.</p>
                  </div>
                  <div class="detail-item">
                      <h3>📍 Location-Based Search</h3>
                      <p>Use our interactive map to locate shelters and foster homes near you. Customize your search based on location, type of care, and more to find the best options for you and the animals.</p>
                  </div>
                  <div class="detail-item">
                      <h3>💬 Contact and Support</h3>
                      <p>Get in touch with shelters and foster homes for more information or to offer your support. Our platform provides direct contact options and support resources to help you connect.</p>
                  </div>
              </div>
              <p>
                  By engaging with our "Shelter and Foster Homes" feature, you contribute to the well-being of animals in need and support the vital work of shelters and foster caregivers in our community.
              </p>
          </div>
      </section>

      <section class="dashboard-feature donation-feature">
          <div class="feature-header" id="donation">
              <h2>Support Our Cause</h2>
          </div>
          <div class="feature-content">
              <p>
                  The "Support Our Cause" feature enables users to contribute to StraySaver’s mission through various donation methods. Whether you choose to make a monetary donation or contribute in-kind items, your support directly helps stray dogs in need.
              </p>
              <div class="feature-details">
                  <div class="detail-item">
                      <h3>💵 Monetary Donations</h3>
                      <p>Your financial contributions help cover essential costs such as medical care, food, and shelter improvements. You can choose to make a one-time donation or set up a recurring monthly contribution.</p>
                  </div>
                  <div class="detail-item">
                      <h3>🎁 In-Kind Donations</h3>
                      <p>We accept various items like dog food, blankets, and toys. These donations help us provide comfort and care to the dogs. You can drop off items at our shelter or arrange for a pickup if needed.</p>
                  </div>
                  <div class="detail-item">
                      <h3>💰 Donation Amount</h3>
                      <p>Select a predefined amount or enter a custom donation amount to contribute. Every bit helps in making a significant impact.</p>
                  </div>
                  <div class="detail-item">
                      <h3>🔁 Frequency</h3>
                      <p>Choose whether your donation is a one-time gift or a recurring monthly contribution. Regular donations ensure continuous support for the dogs.</p>
                  </div>
              </div>
              <p>
                  Your generosity allows us to rescue, rehabilitate, and find forever homes for stray dogs. To make a donation, visit our donation page or contact us for any special arrangements or large contributions.
              </p>
          </div>
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
    const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;
let currentSlide = 0;

function showSlide(index) {
    const offset = -index * 100;
    document.querySelector('.slider').style.transform = `translateX(${offset}%)`;
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
}

document.addEventListener('DOMContentLoaded', () => {
    showSlide(currentSlide);
    setInterval(nextSlide, 3000); // Change slide every 3 seconds

    document.querySelector('.next').addEventListener('click', nextSlide);
    document.querySelector('.prev').addEventListener('click', prevSlide);
});

    </script>

</body>
</html>
