<?php
session_start();
require_once 'utils/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch updated stray dog reports for the logged-in user
$query = "SELECT id,status FROM lostandfound WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lost_reports = [];
while ($row = $result->fetch_assoc()) {
    $lost_reports[] = $row;
}

// Return updated reports as JSON
echo json_encode($lost_reports);