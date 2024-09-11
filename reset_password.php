<?php
session_start();
date_default_timezone_set('Asia/Colombo'); // Set the correct time zone

require_once 'utils/connect.php'; // Adjust to your connection file

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['error_message'] = 'Invalid token.';
    header("Location: forgot_password.php");
    exit();
}

// First, validate the token and check if it has not expired
$stmt = $conn->prepare("SELECT * FROM registration WHERE reset_token=? AND reset_expiry > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Token is invalid or expired
    $_SESSION['error_message'] = 'Invalid or expired token.';
    header("Location: forgot_password.php");
    exit();
}

// Token is valid, proceed to password reset if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if the passwords match
        if ($password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Update the password and clear the reset token and expiry
            $update_sql = "UPDATE registration SET password=?, reset_token=NULL, reset_expiry=NULL WHERE reset_token=?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $hashed_password, $token);

            if ($update_stmt->execute()) {
                // Success message and redirect to login page
                $_SESSION['success_message'] = 'Your password has been updated successfully. You can now log in.';
                header("Location: Login.php");
                exit();
            } else {
                $_SESSION['error_message'] = 'Failed to update password. Please try again.';
            }
        } else {
            // Passwords do not match
            $_SESSION['error_message'] = 'Passwords do not match.';
        }
    } else {
        // Password or confirm password is missing
        $_SESSION['error_message'] = 'Please enter and confirm your new password.';
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
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

        .reset-password-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .reset-password-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .reset-password-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
            font-size: 16px;
        }

        .reset-password-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
            position: relative;
        }

        .reset-password-container input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        .reset-password-container .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #007BFF;
        }

        .reset-password-container button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .reset-password-container button:hover {
            background-color: #0056b3;
            transform: scale(1.02);
        }

        .reset-password-container a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007BFF;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .reset-password-container a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        /* Responsive Styles */
        @media (max-width: 600px) {
            .reset-password-container {
                padding: 15px;
            }

            .reset-password-container h2 {
                font-size: 20px;
            }

            .reset-password-container input[type="password"] {
                padding: 10px;
            }

            .reset-password-container button {
                padding: 10px;
                font-size: 14px;
            }

            .reset-password-container a {
                font-size: 14px;
            }
        }

        @media (max-width: 400px) {
            .reset-password-container {
                padding: 10px;
            }

            .reset-password-container h2 {
                font-size: 18px;
            }

            .reset-password-container input[type="password"] {
                padding: 8px;
            }

            .reset-password-container button {
                padding: 8px;
                font-size: 12px;
            }

            .reset-password-container a {
                font-size: 12px;
            }
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            position: relative;
            padding-bottom: 10px;
        }

        h2::before {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            width: 50px;
            height: 4px;
            background-color: #007BFF;
            transform: translateX(-50%);
        }
    </style>
</head>
<body>
    <h2>Reset Password</h2>
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
    <div class="reset-password-container">
        <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
            <label for="password">New Password:</label>
            <div style="position: relative;">
                <input type="password" id="password" name="password" required>
                <i class="fa fa-eye toggle-password" id="toggle-password" onclick="togglePassword()"></i>
            </div>
            
            <label for="confirm_password">Confirm Password:</label>
            <div style="position: relative;">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <i class="fa fa-eye toggle-password" id="toggle-confirm-password" onclick="toggleConfirmPassword()"></i>
            </div>
            
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
        <a href="Login.php">Back to Login</a>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById('password');
            var icon = document.getElementById('toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function toggleConfirmPassword() {
            var confirmPasswordField = document.getElementById('confirm_password');
            var icon = document.getElementById('toggle-confirm-password');
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
