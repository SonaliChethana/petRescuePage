<?php
require_once 'utils/connect.php'; // Database connection

if (isset($_POST['report_id']) && isset($_POST['responsibility'])) {
    $report_id = $_POST['report_id'];
    $responsibility = $_POST['responsibility'];
    $additional_info = isset($_POST['additional_info']) ? $_POST['additional_info'] : null;
    $other_entity = isset($_POST['other_entity']) ? $_POST['other_entity'] : null;

    // Determine the responsible entity
    if (strpos($responsibility, 'volunteer-') === 0) {
        $responsible_entity = 'Volunteer - ' . $additional_info; // Extract and use additional info
    } elseif (strpos($responsibility, 'shelter-') === 0) {
        $responsible_entity = 'Animal Shelter - ' . $additional_info; // Extract and use additional info
    } elseif (strpos($responsibility, 'vet_clinic-') === 0) {
        $responsible_entity = 'Vet Clinic - ' . $additional_info; // Extract and use additional info
    } elseif ($responsibility === 'other' && $other_entity) {
        $responsible_entity = $other_entity;
    } else {
        echo 'Invalid responsibility type or missing details.';
        exit;
    }

    // Update the emergency report in the database
    $sql = "UPDATE emergencyreport SET responsible_entity = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $responsible_entity, $report_id);

        if ($stmt->execute()) {
            echo "Responsibility claimed successfully!";
        } else {
            echo "Error executing statement: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "Required data missing.";
}

$conn->close();
?>
