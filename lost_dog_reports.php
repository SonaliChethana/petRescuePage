<?php
session_start();
require_once 'utils/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch stray dog reports for the logged-in user
$query = "SELECT id, dog_name, dog_age, dog_description, last_seen_location, photos, date, time, status, created_at FROM lostandfound WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$lost_reports = [];
while ($row = $result->fetch_assoc()) {
    $lost_reports[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Lost Dog Reports</title>
    <!-- Include your CSS stylesheets here -->
    <style>
body {
            font-family: 'Arial', sans-serif;
            background-color: #e9ecef;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
            font-weight: bold;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #ff5722; /* Vibrant Orange */
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .back-button:hover {
            background-color: #e64a19; /* Darker Orange */
            transform: scale(1.05);
        }

        .report {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .report:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .report h2 {
            margin-top: 0;
            color: #00bcd4; /* Teal Color */
            font-size: 1.6em;
            font-weight: bold;
        }

        .report p {
            margin: 8px 0;
            color: #555;
        }

        .report p strong {
            color: #333;
        }

        /* Status Colors */
        .status {
            color: #fff;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            text-transform: capitalize;
            font-weight: bold;
        }

        .status.pending {
            background-color: #ff9800; /* Orange for pending */
        }

        .status.resolved {
            background-color: #4caf50; /* Green for resolved */
        }

        .status.in_progress {
            background-color: #2196f3; /* Blue for in-progress */
        }

        /* Responsible Entity */
        .responsible-entity {
            background-color: #f1f8e9;
            padding: 6px 12px;
            border-radius: 12px;
            display: inline-block;
            font-weight: bold;
            color: #388e3c;
        }

        .date {
            font-size: 0.9em;
            color: #777;
            font-style: italic;
        }

        /* Responsive Design */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .report {
                padding: 15px;
            }

            h1 {
                font-size: 2em;
            }

            .back-button {
                font-size: 14px;
            }
        }
         </style>
</head>
<body>
<a href="profile.php" class="back-button">Back</a>

    <h1>Your Lost Dog Reports</h1>
    <?php if (!empty($lost_reports)): ?>
        <?php foreach ($lost_reports as $report): ?>
            <div class="report">
                <h2><a href="lost_report_details.php?id=<?php echo $report['id']; ?>">Report #<?php echo $report['id']; ?></a></h2>
                <p><strong>Dog Name:</strong> <?php echo htmlspecialchars($report['dog_name']); ?></p>
                <p><strong>Dog Age:</strong> <?php echo htmlspecialchars($report['dog_age']); ?></p>
                <p><strong>Dog Description:</strong> <?php echo htmlspecialchars($report['dog_description']); ?></p>
                <p><strong>Last Seen Location:</strong> <?php echo htmlspecialchars($report['last_seen_location']); ?></p>
                <p><strong>Last seen Date:</strong> <?php echo htmlspecialchars($report['date']); ?></p>
                <p><strong>Last seen Time:</strong> <?php echo htmlspecialchars($report['time']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($report['status']); ?></p>
                <p><strong>Created Date:</strong> <?php echo htmlspecialchars($report['created_at']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have not submitted any lost dog reports.</p>
    <?php endif; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        // Function to fetch updated report data via AJAX
        function fetchUpdatedReports() {
            $.ajax({
                url: 'fetch_updated_lost_reports.php', // This will call another PHP script to get the latest data
                method: 'POST',
                success: function(response) {
                    var reports = JSON.parse(response);
                    reports.forEach(function(report) {
                        // Update the Responsible Entity
                        $('#responsible_entity_' + report.id).text(report.responsible_entity);
                        // Update the Status
                        $('#status_' + report.id).text(report.status);
                    });
                }
            });
        }

        // Set interval to periodically fetch updates every 10 seconds
        setInterval(fetchUpdatedReports, 10000); // Every 10 seconds
    </script>
</body>
</html>
