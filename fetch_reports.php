<?php
session_start();
require_once 'utils/connect.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    echo "You need to log in first.";
    exit();
}

$userId = $_SESSION['user_id'];

// Query to fetch stray dog reports for the logged-in user
$query = "SELECT * FROM reportstray WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

echo json_encode($reports);
?>
