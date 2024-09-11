<?php
session_start();
require_once 'utils/connect.php'; // Ensure this file contains the connection to your database

if (isset($_GET['code'])) {
    $verificationCode = $_GET['code'];

    // Check if the verification code exists in the database
    $stmt = $conn->prepare("SELECT * FROM registration WHERE verification_code = ?");
    if ($stmt === false) {
        die("MySQL prepare statement error: " . $conn->error);
    }

    $stmt->bind_param("s", $verificationCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the user account has been created already
        if ($user['is_verified'] == 0) {
            // If the code exists, verify the email and update the record
            $updateStmt = $conn->prepare("UPDATE registration SET is_verified = 1, account_created = 1 WHERE verification_code = ?");
            if ($updateStmt === false) {
                die("MySQL prepare statement error: " . $conn->error);
            }

            $updateStmt->bind_param("s", $verificationCode);

            if ($updateStmt->execute()) {
                // Redirect to login page after successful verification
                header("Location: Login.php?verified=1");
                exit();
            } else {
                echo "Error updating record: " . $updateStmt->error;
            }
        } else {
            echo "Account already verified.";
        }
    } else {
        echo "Invalid verification code.";
    }
} else {
    echo "No verification code provided.";
}

$conn->close();
?>
