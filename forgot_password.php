<?php
session_start();
date_default_timezone_set('Asia/Colombo'); // Set the correct time zone

require_once 'utils/connect.php'; // Adjust to your connection file
require_once 'vendor/autoload.php'; // Load Composer's autoloader for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set the time zone for the database connection
$conn->query("SET time_zone = 'Asia/Colombo'");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM registration WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token for password reset
        $reset_token = bin2hex(random_bytes(16));
        $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update the user's reset token and expiry
        $sql = "UPDATE registration SET reset_token=?, reset_expiry=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $reset_token, $reset_expiry, $email);
        $stmt->execute();

        // Prepare the password reset link
        $reset_link = "http://localhost/petRescue/reset_password.php?token=" . $reset_token;

        // Send the email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();                                           // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                      // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                  // Enable SMTP authentication
            $mail->Username   = 'straysaver10@gmail.com';              // SMTP username
            $mail->Password   = 'nfsy yjqk tttj psgg';                       // SMTP password (or app password if using Gmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Enable TLS encryption
            $mail->Port       = 587;                                   // TCP port to connect to

            // Recipients
            $mail->setFrom('straysaver10@gmail.com', 'StraySaver');
            $mail->addAddress($email);                                // Add a recipient

            // Content
            $mail->isHTML(true);                                      // Set email format to HTML
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the following link to reset your password: <a href=\"$reset_link\">$reset_link</a>";
            $mail->AltBody = "Click the following link to reset your password: $reset_link"; // Plain text version

            // Send email
            $mail->send();
            $_SESSION['success_message'] = 'A password reset link has been sent to your email address.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Failed to send email. Error: ' . $mail->ErrorInfo;
        }

    } else {
        $_SESSION['error_message'] = 'Email not found.';
    }
    $stmt->close();
    header("Location: forgot_password.php");
    exit();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>


    <style>

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        /* Background Styling */
        body {
            background: linear-gradient(#f7f5f0, #f0ddd9);
            font-family: 'Poppins', sans-serif;
            align-items: center;
            justify-content: center;
            height: 100vh;
            
        }


        /* Header */
        .header {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('images/bg-1.jpg') no-repeat center center/cover; /* Gradient overlay with background image */
            color: #fff; /* Text color for readability */
            padding: 20px 20px; /* Padding around the header content */
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


        /* Responsive Styles for Header */
        @media (max-width: 1200px) {
            .header {
                padding: 15px; /* Adjust padding for larger tablets and smaller desktops */
            }

            .logo img {
                max-width: 80px; /* Reduce logo size for smaller screens */
                height: 80px; /* Adjust height for smaller screens */
            }

            .title {
                font-size: 2em; /* Reduce font size for title */
                margin-right: 50px; /* Reduce margin for smaller screens */
            }

            .title::before,
            .title::after {
                font-size: 1em; /* Adjust icon size */
            }

            .nav {
                margin-right: 10px; /* Reduce margin for navigation */
            }

            .nav-list {
                margin-right: 10px; /* Adjust margin for list */
            }
        }

        @media (max-width: 992px) {
            .header {
                flex-direction: column; /* Stack logo and title vertically */
                align-items: flex-start; /* Align items to the start */
                padding: 15px; /* Adjust padding */
            }

            .logo {
                margin-bottom: 10px; /* Space below logo */
            }

            .title {
                font-size: 1.8em; /* Further reduce font size for title */
                margin-right: 0; /* Remove margin for better alignment */
            }

            .nav {
                margin: 10px 0; /* Space above and below navigation */
                width: 100%; /* Full width for navigation */
                justify-content: flex-start; /* Align items to the start */
            }

            .nav-list {
                flex-direction: column; /* Stack navigation items vertically */
                width: 100%; /* Full width for navigation list */
            }

            .nav-item {
                margin-left: 0; /* Remove left margin for vertical layout */
                margin-bottom: 10px; /* Space between navigation items */
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 10px; /* Adjust padding for smaller tablets and larger phones */
            }

            .logo img {
                max-width: 60px; /* Further reduce logo size */
                height: 60px; /* Adjust height */
            }

            .title {
                font-size: 1.6em; /* Further reduce font size for title */
            }

            .title::before,
            .title::after {
                font-size: 0.9em; /* Adjust icon size */
            }

            .nav {
                margin: 10px 0; /* Adjust margin */
                width: 100%; /* Full width for navigation */
            }

            .nav-list {
                flex-direction: column; /* Stack navigation items vertically */
                width: 100%; /* Full width for navigation list */
            }

            .nav-item {
                margin-left: 0; /* Remove left margin */
                margin-bottom: 10px; /* Space between items */
            }

            .nav-link {
                padding: 8px 16px; /* Adjust padding for navigation links */
            }
        }

        @media (max-width: 576px) {
            .header {
                padding: 10px 5px; /* Reduce padding for small screens */
            }

            .logo img {
                max-width: 50px; /* Further reduce logo size */
                height: 50px; /* Adjust height */
            }

            .title {
                font-size: 1.4em; /* Further reduce font size */
            }

            .title::before,
            .title::after {
                font-size: 0.8em; /* Adjust icon size */
            }

            .nav {
                width: 100%; /* Full width for navigation */
            }

            .nav-list {
                flex-direction: column; /* Stack navigation items vertically */
                width: 100%; /* Full width for navigation list */
            }

            .nav-item {
                margin-left: 0; /* Remove left margin */
                margin-bottom: 8px; /* Space between items */
            }

            .nav-link {
                padding: 6px 12px; /* Adjust padding for small screens */
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


        /* Container Styling */
.forgot-password-container {
    background-color: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    height: auto;
    text-align: center;
    margin: auto;
    transition: transform 0.3s ease, background-color 0.3s ease;
    position: relative;
    top: 40px;
    border: 1px solid #e0e0e0;
    animation: fadeIn 0.5s ease;
}

.forgot-password-container:hover {
    transform: scale(1.03);
    background-color: #f9f9f9;
}

/* Form Title Styling */
.forgot-password-form h2 {
    color: #333;
    font-size: 26px;
    margin-bottom: 20px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

/* Label Styling */
.forgot-password-form label {
    font-size: 16px;
    color: #555;
    margin-bottom: 10px;
    display: block;
    font-weight: 500;
}

/* Input Styling */
.forgot-password-form input[type="email"] {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    background-color: #f7f9fc;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

.forgot-password-form input[type="email"]:focus {
    outline: none;
    border-color: #007bff;
    background-color: #fff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

/* Button Styling */
.forgot-password-form button {
    background-color: #007bff;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
    margin-top: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.forgot-password-form button:hover {
    background-color: #0056b3;
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.forgot-password-form button:focus {
    outline: none;
}

/* Back to Login Link Styling */
.back-link {
    display: block;
    margin-top: 20px;
    font-size: 14px;
    color: #007bff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.back-link:hover {
    color: #0056b3;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsiveness for Mobile Screens */
@media (max-width: 600px) {
    .forgot-password-container {
        padding: 30px;
        top: 0;
        border-radius: 8px;
    }

    .forgot-password-form h2 {
        font-size: 22px;
    }

    .forgot-password-form button {
        font-size: 14px;
    }

    .forgot-password-form input[type="email"] {
        padding: 10px;
        font-size: 14px;
    }

    .back-link {
        font-size: 13px;
    }
}

/* Responsiveness for Tablet Screens */
@media (min-width: 601px) and (max-width: 992px) {
    .forgot-password-container {
        max-width: 500px;
        padding: 35px;
    }

    .forgot-password-form h2 {
        font-size: 24px;
    }

    .forgot-password-form input[type="email"] {
        padding: 12px;
        font-size: 16px;
    }

    .forgot-password-form button {
        font-size: 15px;
    }
}

/* Responsiveness for Large Screens */
@media (min-width: 1200px) {
    .forgot-password-container {
        max-width: 450px;
    }

    .forgot-password-form h2 {
        font-size: 28px;
    }

    .forgot-password-form input[type="email"] {
        padding: 14px;
        font-size: 18px;
    }

    .forgot-password-form button {
        font-size: 18px;
    }
}


.error {
            color: #e74c3c;
            background-color: #fdd;
            padding: 10px;
            border: 1px solid #e74c3c;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            color: #27ae60;
            background-color: #d4edda;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
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
                    <li class="nav-item"><a href="Login.php" class="nav-link login-button">Back</a></li>
                </ul>
            </nav>
        </div>
    </header>

   
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<p class="error">' . $_SESSION['error_message'] . '</p>';
        unset($_SESSION['error_message']);
    }
    if (isset($_SESSION['success_message'])) {
        echo '<p class="success">' . $_SESSION['success_message'] . '</p>';
        unset($_SESSION['success_message']);
    }
    ?>
    <div class="forgot-password-container">
    <form method="POST" action="forgot_password.php" class="forgot-password-form">
        <h2>Forgot Password</h2>
        <label for="email">Enter your email address:</label><br />
        <input type="email" id="email" name="email" placeholder="Email" required><br />
        <button type="submit">Send Reset Link</button>
    </form>
    <a href="Login.php" class="back-link">Back to Login</a>
</div>
</body>
</html>
