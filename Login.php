<?php
session_start();
require_once 'utils/connect.php'; // Adjust this to your actual connection file

// Initialize error and success messages
$error_message = '';
$success_message = '';

// Check if form was submitted for login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Check if username and password are set
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Get form data
        $user = $_POST['username'];
        $pass = $_POST['password'];

        // Prepare and execute query
        $sql = "SELECT * FROM registration WHERE username=? OR email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user, $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_role'] = $row['user_role'];
                    $_SESSION['username'] = $row['username']; // Store username if needed

                    // Redirect based on user role
                    switch ($row['user_role']) {
                        case 'dogLover':
                            header("Location: Dashboard.php");
                            break;
                        case 'vetClinic':
                            header("Location: Dashboard.php");
                            break;
                        case 'animalShelter':
                            header("Location: Dashboard.php");
                            break;
                        default:
                            header("Location: Login.php");
                            break;
                    }
                    exit();
                } else {
                    $_SESSION['error_message'] = 'Please verify your email before logging in.';
                }
            } else {
                // Invalid password
                $_SESSION['error_message'] = 'Invalid password. Please try again.';
            }
        } else {
            // User not found
            $_SESSION['error_message'] = 'User does not exist.';
        }


        $stmt->close();
    } else {
        // Username or password not set
        $_SESSION['error_message'] = '';
    }


// Check if form was submitted for forgot password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forgot_password'])) {
    // Check if email is set
    if (isset($_POST['email'])) {
        // Get form data
        $email = $_POST['email'];

        // Prepare and execute query
        $sql = "SELECT * FROM registration WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $password = $row['password'];

            $reset_token = bin2hex(random_bytes(16));
            $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $sql = "UPDATE registration SET reset_token=?, reset_expiry=? WHERE email=?";   
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $reset_token, $reset_expiry, $email);
            $stmt->execute();

             // Send reset link via email
             $reset_link = "http://yourdomain.com/reset_password.php?token=" . $reset_token;
             $to = $email;
             $subject = "Password Reset Request";
             $message = "Click the following link to reset your password: " . $reset_link;
             $headers = "From: no-reply@straysaver.com";

             if (mail($to, $subject, $message, $headers)) {
                $_SESSION['success_message'] = 'A password reset link has been sent to your email address.';
            } else {
                $_SESSION['error_message'] = 'Failed to send email. Please try again later.';
            }
        } else {
            // Email not found
            $_SESSION['error_message'] = 'Email not found.';
        }

        $stmt->close();
    } else {
        // Email not set
        $_SESSION['error_message'] = 'Please enter your email address.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Saver - Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
    
    background: url('images/login-bg.jpg') no-repeat center center fixed; 
    background-size: cover; /* Ensure the background covers the whole page */
}

.header {
    background: rgba(0, 0, 0, 0.6); /* Dark overlay for the header */
    color: #fff; /* Text color for readability */
    padding: 20px 20px; /* Padding around the header content */
    border-radius: 8px; /* Rounded corners */
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

    .title::before, .title::after {
        font-size: 1em; /* Adjust icon size */
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
        font-size: 0.9em; /* Adjust icon size */
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
                    <li class="nav-item"><a href="index.php" class="nav-link home-button">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="welcome-section">
            <div class="welcome-text">
                <img src="images/paw.jpg" alt="Paw Logo" class="welcome-img">
                <div>
                    <h2>Welcome Back!</h2>
                    <p class="login-message">Login to access your account and manage your saved pets.</p>
                    <p class="signup-message">New here? Join our community to help rescue and find homes for stray pets!</p>

                </div>
            </div>
            <div class="button1">
                <a href="Registration.php" class="sign-up">Create Account</a>
            </div>
        </div>
        <div class="login-section">
            <div class="login-form">
                <h2>Login</h2>
                <?php
                if (isset($_SESSION['error_message']) && $_SESSION['error_message'] != '') {
                    echo '<p class="error">' . $_SESSION['error_message'] . '</p>';
                    unset($_SESSION['error_message']);
                }
                if (isset($_SESSION['success_message']) && $_SESSION['success_message'] != '') {
                    echo '<p class="success">' . $_SESSION['success_message'] . '</p>';
                    unset($_SESSION['success_message']);
                }
                ?>
                <form method="POST" action="login.php">
                    <input type="hidden" name="login" value="1">
                    <label for="username">Username/Email</label><br />
                    <input type="text" id="username" name="username" placeholder="Username or email" required><br />
                    <label for="password">Password</label><br />
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span id="toggle-password" onclick="togglePassword()">Show password</span>
                    <br /><br />
                    <button type="submit" class="button2">Login</button>
                    <p class="error" id="error"></p>
                    <p class="forgot-password-link"><a href="forgot_password.php"  onclick="showForgotPassword()">Forgot Password?</a></p>

                </form>
                <div class="create-account">
                    <p>Don't have an account? <a href="Registration.php">Create account</a></p>
                </div>
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

    </main>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                togglePassword.innerText = 'Hide password';
            } else {
                passwordField.type = 'password';
                togglePassword.innerText = 'Show password';
            }
        }

        function showForgotPassword() {
        document.querySelector('.login-section').style.display = 'none';
        document.querySelector('.forgot-password-section').style.display = 'block';
        document.querySelector('.btn-back').style.display = 'block'; // Ensure the button is visible

    }

    function showLoginForm() {
            document.querySelector('.forgot-password-section').style.display = 'none';
            document.querySelector('.login-section').style.display = 'flex';
            document.querySelector('.btn-back').style.display = 'none'; // Hide the button



        }


    </script>
</body>
</html>