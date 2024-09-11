<?php
session_start();
require_once 'utils/connect.php'; // Ensure this file contains the connection to your database
require_once 'vendor/autoload.php'; // Load Composer's autoloader


// Initialize error and success messages
$error = array();
$success_message = '';



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userRole = $_POST['userRole']; // Get the user role

    // Common variables
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Role-specific variables
    $fullName = $clinicName = $shelterName = "";
     
     // Dog Lover Registration
     if ($userRole == 'dogLover') {
        $fullName = trim($_POST['fullName']);

        if (empty($fullName)) {
            $errors[] = "Full Name is required for Dog Lover profile.";
        }
        
    } elseif ($userRole == 'vetClinic') {
        $clinicName = trim($_POST['clinicName']);
        $fullName = $shelterName = "";

        if (empty($clinicName)) {
            $errors[] = "Clinic Name is required.";
        }

    } elseif ($userRole == 'animalShelter') {
        $shelterName = trim($_POST['shelterName']);
        $fullName = $clinicName = "";

        if (empty($shelterName)) {
            $errors[] = "Shelter Name is required.";
        }
    }

    // Common validations
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $password)) {
        $errors[] = "Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, and one number.";
    }

    if (empty($errors)) {
        // Hash the password before saving to the database

    
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Generate a unique verification code
        $verificationCode = md5(time() . $email);
        $isVerified = 0;
        $accountCreated = 0;


        if ($userRole == 'dogLover') {
            $stmt = $conn->prepare("INSERT INTO registration (full_name, email, username, password, user_role, verification_code, account_created) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $fullName, $email, $username, $passwordHash, $userRole, $verificationCode, $accountCreated);
        } elseif ($userRole == 'vetClinic') {
            $stmt = $conn->prepare("INSERT INTO registration (clinic_name, email, username, password, user_role, verification_code, account_created) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $clinicName, $email, $username, $passwordHash, $userRole, $verificationCode, $accountCreated);
        } elseif ($userRole == 'animalShelter') {
            $stmt = $conn->prepare("INSERT INTO registration (shelter_name, email, username, password, user_role, verification_code, account_created) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $shelterName, $email, $username, $passwordHash, $userRole , $verificationCode, $accountCreated);
        }

            
        if ($stmt->execute()) {
           
            // Update the has_vet_clinic field if the user role is 'vetClinic'
            if ($userRole == 'vetClinic') {
                $userId = $stmt->insert_id; // Get the ID of the newly inserted user
                $update_sql = "UPDATE registration SET has_vet_clinic = 1 WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $userId);
                $update_stmt->execute();
                $update_stmt->close();
            }

         // Update the has_animal_shelter field if the user role is 'animalShelter'
            if ($userRole == 'animalShelter') {
                $userId = $stmt->insert_id; // Get the ID of the newly inserted user
                $update_sql = "UPDATE registration SET has_animal_shelter = 1 WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $userId);
                $update_stmt->execute();
                $update_stmt->close();
            }
        
            // Send verification email using SwiftMailer
            $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
              ->setUsername('straysaver10@gmail.com')
              ->setPassword('nfsy yjqk tttj psgg');

            $mailer = new Swift_Mailer($transport);

            $message = (new Swift_Message('Please verify your email address'))
              ->setFrom(['straysaver10@gmail.com' => 'StraySaver'])
              ->setTo([$email])
              ->setBody("Click the following link to verify your email address: 
                http://localhost:8080/petRescue/verify_email.php?code=" . urlencode($verificationCode));

            try {
                $result = $mailer->send($message);
                $success_message = "Registration successful! Please check your email to verify your account.";
            } catch (Exception $e) {
                $errors[] = "Failed to send verification email. Error: " . $e->getMessage();
            }
        } else {
            // Pass error message to JavaScript
            $error_message = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $errors[] = $error_message;
        }
    
        $stmt->close();

    }

    
$conn->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Saver Registration</title>
    <link rel="stylesheet" href="css/registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>



    
    <style>
          
          body {
            background: url('images/bg.jpg') no-repeat center center fixed; 
            background-size: cover; /* Ensure the background covers the whole page */
          }
          .hidden {
            display: none;
        }
    

.header {
    background: rgba(0, 0, 0, 0.6); /* Dark overlay for the header */
    color: #fff; /* Text color for readability */
    padding: 20px 20px; /* Padding around the header content */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
    position: absolute; /* Position it within the hero */
    top: 0;
    left: 0;
    width: 100%;
    z-index: 2; /* Ensure it is above the hero content */
    display: flex;
    justify-content: space-between; /* Push navigation to the right */
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
    margin-right: auto; /* Push logo to the left */
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
    font-size: 2em; /* Larger font size for emphasis */
    color: #f4be77; /* Warm color */
    text-transform: uppercase; /* Capitalize for emphasis */
   
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
}

.nav-list {
    display: flex;
    justify-content: flex-end; /* Push navigation buttons to the right */
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-item {
    margin-left: 20px; /* Space between nav items */
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
        font-size: 1.4em;
        margin-right: 0;
        margin-bottom: 10px;
    }

    .nav {
        flex-direction: column; /* Stack navigation items vertically */
        margin-top: 10px;
    }

    .nav-list {
        flex-direction: column; /* Stack navigation items vertically */
        justify-content: center;
        width: 100%; /* Ensure it takes full width */
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
        font-size: 1.2em;
        margin-right: 0;
        margin-bottom: 10px;
    }

    .title::before, .title::after {
        font-size: 1em; /* Adjust icon size */
    }

    .nav {
        flex-direction: column; /* Stack navigation items vertically */
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

.hero {
    background: url('images/hero.jpg') no-repeat center center/cover; /* Background image with cover fit */
    color: #fff; /* Text color for readability against the background */
    padding: 80px 20px; /* Spacing around the text */
    text-align: center; /* Center-align text */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Shadow for depth */
    position: relative; /* Position for overlay effect */
    overflow: hidden; /* Hide any overflow from child elements */
    max-width: 1600px; /* Maximum width of the hero section */
    margin: 0 auto; /* Center-align the hero section horizontally */
}
.hero-content {
    margin-top: 100px; /* Space below header */
}

.hero-content h1 {
    font-size: 48px; /* Large, bold heading */
    margin-bottom: 20px; /* Space below the heading */
    line-height: 1.2; /* Better line spacing for readability */
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6); /* Subtle text shadow for contrast */
}

.hero-content p {
    font-size: 20px; /* Slightly smaller than the heading */
    max-width: 800px; /* Limit width for better readability */
    margin: 0 auto; /* Center-align the paragraph */
    line-height: 1.6; /* Better spacing between lines */
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6); /* Subtle text shadow for contrast */
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

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

        
        /* Form Container Styling */
        section {
            margin: 20px auto;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .user-type-selection {
            margin: 20px auto;
            padding: 20px;
            max-width: 400px;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .user-role-label {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        .custom-select-wrapper {
            position: relative;
            width: 100%;
        }

        .user-role-dropdown {
            width: 100%;
            padding: 10px 40px 10px 15px; /* Padding to account for the arrow */
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            appearance: none; /* Remove default dropdown arrow */
            cursor: pointer;
            transition: all 0.3s ease;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iOCIgdmlld0JveD0iMCAwIDE2IDgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTEgMUwxNCAxTDggNyIgZmlsbD0iIzAwN2JmZiIgc3Ryb2tlPSJub25lIi8+PC9zdmc+'); /* Custom arrow image */
            background-repeat: no-repeat;
            background-position: right 10px center; /* Position the arrow */
            background-size: 16px 8px; /* Size of the arrow */
        }

        .user-role-dropdown:hover {
            border-color: #007bff;
            box-shadow: inset 0 2px 4px rgba(0, 123, 255, 0.2);
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .user-role-dropdown {
                font-size: 14px; /* Slightly smaller font size on tablets */
                padding: 8px 35px 8px 12px; /* Adjust padding for smaller screens */
                background-position: right 8px center; /* Adjust arrow position */
                background-size: 14px 7px; /* Adjust arrow size */
            }
        }

        @media (max-width: 480px) {
            .user-role-dropdown {
                font-size: 12px; /* Smaller font size on mobile */
                padding: 7px 30px 7px 10px; /* Further adjust padding */
                background-position: right 6px center; /* Adjust arrow position */
                background-size: 12px 6px; /* Further adjust arrow size */
            }
        }



        /* General Form Styling */
            .form-section {
                max-width: 400px; /* Set max width for a smaller form */
                margin: 20px auto; /* Center the form */
                padding: 25px;
                background-color: #f9f9f9;
                border-radius: 12px;
                box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                font-family: 'Arial', sans-serif;
            }

            .form-section h2 {
                font-size: 26px;
                margin-bottom: 15px;
                text-align: center;
                color: #333;
                font-weight: bold;
                letter-spacing: 1px;
            }

            .form-group {
                margin-bottom: 18px;
            }

            .form-group label {
                display: block;
                font-weight: bold;
                margin-bottom: 6px;
                font-size: 15px;
                color: #555;
                text-transform: uppercase;
            }

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="password"],
            .form-group select {
                width: 100%;
                padding: 10px;
                font-size: 14px;
                border-radius: 8px;
                border: 1px solid #ccc;
                background-color: #fff;
                box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
            }

            .form-group input[type="text"]:focus,
            .form-group input[type="email"]:focus,
            .form-group input[type="password"]:focus,
            .form-group select:focus {
                border-color: #3f51b5;
                outline: none;
                box-shadow: 0 0 8px rgba(63, 81, 181, 0.5);
            }

        /* Submit Button Styling */
            button[type="submit"] {
                width: 100%;
                padding: 12px;
                background-color: #ff9900;
                color: #fff;
                font-size: 16px;
                font-weight: bold;
                text-transform: uppercase;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
                margin-top: 20px;
                box-shadow: 0 4px 10px rgba(63, 81, 181, 0.3);
            }

            button[type="submit"]:hover {
                background-color: #cc7a00;
                box-shadow: 0 6px 14px rgba(48, 63, 159, 0.4);
                transform: translateY(-2px);
            }

            button[type="submit"]:active {
                background-color: #cc7a00;
                box-shadow: 0 2px 5px rgba(48, 63, 159, 0.2);
                transform: translateY(0);
            }

        /* Error and Success Messages */
        .error, .success-message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            font-size: 16px;
        }

        .error {
    margin-top: 20px;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    background-color: #f8d7da; /* Light red background */
    color: #721c24; /* Dark red text */
    border: 2px solid #f5c6cb; /* Slightly darker border */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow to add depth */
}

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Adjust form responsiveness */
        @media screen and (max-width: 600px) {
            .form-section {
                padding: 20px;
            }

            .form-group input[type="text"],
            .form-group input[type="email"],
            .form-group input[type="password"],
            .form-group select {
                padding: 10px;
                font-size: 14px;
            }

            button[type="submit"] {
                padding: 10px;
                font-size: 15px;
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

.password-group {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 10px;
    top: 70%; /* Adjusted this value to move the icon a bit lower */
    transform: translateY(-50%);
    cursor: pointer;
}

.toggle-password i {
    font-size: 18px;
    color: #007bff;
}

/* Optional: Add hover effect for the eye icon */
.toggle-password:hover i {
    color: #0056b3;
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
                    <li class="nav-item"><a href="index.php" class="nav-link signup-button">Back</a></li>
                    <li class="nav-item"><a href="login.php" class="nav-link login-button">Log In</a></li>
                </ul>
            </nav>
        </div>
    </header>
   

    <section class="hero">
        <div class="hero-content">
            <h1>Join Us in Saving Stray Dogs</h1>
            <p>Register to become a part of our community dedicated to rescuing and caring for stray dogs.</p>
        </div>
    </section>

        <section class="user-type-selection">
            <label for="userRole" class="user-role-label">Select User Type:</label>
            <select id="userRole" class="user-role-dropdown" onchange="showRegistrationForm()">
                <option value="dogLover">Dog Lover</option>
                <option value="vetClinic">Vet Clinic</option>
                <option value="animalShelter">Animal Shelter/Foster</option>
            </select>
        </section>


        <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif ($success_message): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>

        <!-- Dog Lover Registration Form -->
        <div id="dogLoverForm" class="form-section">
            <h2>Dog Lover Registration</h2>
            <form method="POST" action="registration.php" onsubmit="return validatePasswords()">
                <input type="hidden" name="userRole" value="dogLover">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Enter full name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form-group password-group">
                    <label for="dog-lover-password">Password</label>
                    <input type="password" id="dog-lover-password" name="password" placeholder="Enter password" required
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, and one number.">
                    <span class="toggle-password" onclick="togglePassword('dog-lover-password')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <div class="form-group password-group">
                    <label for="dog-lover-confirmPassword">Confirm Password</label>
                    <input type="password" id="dog-lover-confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                    <span class="toggle-password" onclick="togglePassword('dog-lover-confirmPassword')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <button type="submit">Register</button>
            </form>
        </div>

        

        <!-- Vet Clinic Registration Form -->
        <div id="vetClinicForm" class="form-section hidden">
            <h2>Vet Clinic Registration</h2>
            <form method="POST" action="registration.php" onsubmit="return validatePasswords()">
                <input type="hidden" name="userRole" value="vetClinic">
                <div class="form-group">
                    <label for="clinicName">Clinic Name</label>
                    <input type="text" id="clinicName" name="clinicName" placeholder="Enter clinic name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter clinic email" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form-group password-group">
                    <label for="vet-password">Password</label>
                    <input type="password" id="vet-password" name="password" placeholder="Enter password" required
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, and one number.">
                    <span class="toggle-password" onclick="togglePassword('vet-password')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <div class="form-group password-group">
                    <label for="vet-confirmPassword">Confirm Password</label>
                    <input type="password" id="vet-confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                    <span class="toggle-password" onclick="togglePassword('vet-confirmPassword')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <button type="submit">Register</button>
            </form>
        </div>

        <!-- Animal Shelter Registration Form -->
        <div id="animalShelterForm" class="form-section hidden">
            <h2>Animal Shelter/Foster Registration</h2>
            <form method="POST" action="registration.php" onsubmit="return validatePasswords()">
                <input type="hidden" name="userRole" value="animalShelter">
                <div class="form-group">
                    <label for="shelterName">Shelter Name</label>
                    <input type="text" id="shelterName" name="shelterName" placeholder="Enter shelter name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter shelter email" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
                <div class="form-group password-group">
                    <label for="shelter-password">Password</label>
                    <input type="password" id="shelter-password" name="password" placeholder="Enter password" required
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                    title="Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, and one number.">
                    <span class="toggle-password" onclick="togglePassword('shelter-password')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <div class="form-group password-group">
                    <label for="shelter-confirmPassword">Confirm Password</label>
                    <input type="password" id="shelter-confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                    <span class="toggle-password" onclick="togglePassword('shelter-confirmPassword')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
                <button type="submit">Register</button>
            </form>
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
        
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        function showRegistrationForm() {
            var userRole = document.getElementById("userRole").value;
            document.getElementById("dogLoverForm").style.display = (userRole === 'dogLover') ? 'block' : 'none';
            document.getElementById("vetClinicForm").style.display = (userRole === 'vetClinic') ? 'block' : 'none';
            document.getElementById("animalShelterForm").style.display = (userRole === 'animalShelter') ? 'block' : 'none';
        }


        function validatePasswords() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirmPassword").value;
        
        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return false;
        }
        return true;
    }

    function togglePassword(inputId) {
    const inputField = document.getElementById(inputId);
    const icon = inputField.nextElementSibling.querySelector('i');
    
    if (inputField.type === 'password') {
        inputField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        inputField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}





        <?php if ($success_message): ?>
            Swal.fire({
                icon: 'success',
                title: '<?php echo addslashes($success_message); ?>',
                showConfirmButton: true
            });
        <?php elseif (!empty($errors)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo addslashes(implode(" ", $errors)); ?>'
            });
        <?php endif; ?><?php if ($success_message): ?>
            Swal.fire({
                icon: 'success',
                title: '<?php echo addslashes($success_message); ?>',
                showConfirmButton: true
            });
        <?php elseif (!empty($errors)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '<?php echo addslashes(implode(" ", $errors)); ?>'
            });
        <?php endif; ?>
    </script>
</body>
</html>
