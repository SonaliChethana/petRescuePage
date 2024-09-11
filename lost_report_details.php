<?php
session_start();
require_once 'utils/connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo "No report ID specified.";
    exit();
}

$user_id = $_SESSION['user_id'];
$report_id = $_GET['id'];

// Fetch the report details
$query = "SELECT * FROM lostandfound WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $report_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

if (!$report) {
    echo "Report not found.";
    exit();
}

// If photos are stored as serialized data
$photoPath = @unserialize($report['photos']);
if ($photoPath === false) {
    $photoPath = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Report Details</title>
    <!-- Include your CSS stylesheets here -->
    <style>
 body {
            font-family: 'Roboto', sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            color: #495057;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        h1 {
            font-size: 30px;
            color: #343a40;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .section {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
        }
        .section h2 {
            font-size: 24px;
            color: #343a40;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            font-weight: 500;
        }
        p {
            font-size: 17px;
            line-height: 1.7;
            margin: 10px 0;
        }
        strong {
            color: #007bff;
            font-weight: 600;
        }
        .photos img {
            border-radius: 8px;
            margin: 10px 0;
            max-width: 100%;
            height: auto;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .photos img:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        a.button {
            display: inline-block;
            padding: 12px 28px;
            font-size: 18px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }
        a.button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        a.button:active {
            transform: translateY(1px);
        }
        .report-id {
            font-weight: bold;
            color: #007bff;
        }
         </style>
</head>
<body>
    <div class="container">
    <div class="section">
    <h2>Report #<span class="report-id"><?php echo $report['id']; ?></span></h2>
    <p><strong>Dog Name:</strong> <?php echo htmlspecialchars($report['dog_name']); ?></p>
    <p><strong>Dog Age:</strong> <?php echo htmlspecialchars($report['dog_age']); ?></p>
    <p><strong>Dog Description:</strong> <?php echo htmlspecialchars($report['dog_description']); ?></p>
    <p><strong>Last seen Location:</strong> <?php echo htmlspecialchars($report['last_seen_location']); ?></p>
    <p><strong>Last seen Date:</strong> <?php echo htmlspecialchars($report['date']); ?></p>
    <p><strong>Last seen time:</strong> <?php echo htmlspecialchars($report['time']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($report['status']); ?></p>
    <p><strong>Date Submitted:</strong> <?php echo htmlspecialchars($report['created_at']); ?></p>
    </div>

    <?php if (!empty($photoPath) && is_array($photoPath)): ?>
        <div class="section">
        <h3>Photos:</h3>
        <div class="photos">
        <?php foreach ($photoPath as $photo): ?>
            <img src="<?php echo htmlspecialchars($photo); ?>" alt="Lost Dog Photo" width="200px">
        <?php endforeach; ?>
        </div>
        </div>
    <?php endif; ?>

    <a href="lost_dog_reports.php" class="button">Back to Your Reports</a>
    </div>


     
</body>
</html>
