<?php
session_start();
require_once 'utils/connect.php';  // Update the path as needed

$response = [
    'success' => false,
    'message' => 'Failed to save details'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are set and not empty
    if (isset($_POST['dog_name'], $_POST['your_name'], $_POST['email'])) {
        $dog_name = $_POST['dog_name'];
        $your_name = $_POST['your_name'];
        $email = $_POST['email'];

        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO adoptdog (dog_name, your_name, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $dog_name, $your_name, $email);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Details successfully saved!';
        } else {
            $response['message'] = 'Failed to save details: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = 'Required fields missing';
    }

    echo json_encode($response);
    exit;
}

// Fetch dog profiles for display
$profiles = [];
$query = "SELECT id, photo, dog_name, age, description, video, gender FROM dogprofile";
$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $profiles[] = $row;
        }
    }
} else {
    // Print the error message
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rescue Dogs Adoption</title>
    <link rel="stylesheet" href="css/adopt.css">
    <style>
        /* Add your CSS here */
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="images/logo2.jpg" alt="Logo">
            <h1>Rescue Dogs Adoption</h1>
        </div>
        <nav>
            <a href="Dashboard.php">Home</a>
            <a href="#">About Us</a>
            <a href="dogProfile.php">Dog Profile</a>
            <a href="#">Contact Us</a>
        </nav>
    </header>

    <div class="hero">
        <div class="hero-content">
            <img src="images/adoption.jpeg" alt="Rescue Dog" class="hero-image">
            <div class="hero-text">
                <h2>Meet Our Rescue Dogs</h2>
                <p>Available for Adoption</p>
                <p>Help us find them homes</p>
                <p>Check out our lovely rescue dogs looking for a forever home!</p>
                <button class="donate-button">Donate</button>
                <button id="adoptNowButton" class="adopt-button">Adopt Now</button>
            </div>
        </div>
    </div>

    <div class="profiles">
        <h2>Dog Profiles</h2>
        <div class="slider-container">
            <button id="prevBtn" class="slide-btn">‹</button>
            <div class="profile-list">
                <?php foreach ($profiles as $profile): ?>
                    <div class="profile" onclick="showProfileDetails(<?php echo htmlspecialchars(json_encode($profile)); ?>)">
                        <img src="uploads/<?php echo htmlspecialchars($profile['photo']); ?>" alt="<?php echo htmlspecialchars($profile['dog_name']); ?>">
                        <h3><?php echo htmlspecialchars($profile['dog_name']); ?></h3>
                        <p><?php echo htmlspecialchars($profile['age']); ?> years old</p>
                    </div>
                    <div class="profile-details" id="profile-details-<?php echo $profile['id']; ?>">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['dog_name']); ?></p>
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($profile['age']); ?> years old</p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($profile['description']); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($profile['gender']); ?></p>
                        <?php if (!empty($profile['video'])): ?>
                            <p><strong>Video:</strong> <a href="uploads/<?php echo htmlspecialchars($profile['video']); ?>" target="_blank">Watch Video</a></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button id="nextBtn" class="slide-btn">›</button>
        </div>
    </div>

    <div class="adopt-form" id="adoptForm">
        <h2>Adopt a Dog</h2>
        <p>Fill in the form below to start the adoption process</p>

        <div id="notification" class="notification"></div>

        <form id="adoptDogForm" action="Adoption.php" method="POST">
            <label for="dog_name">Dog's Name</label>
            <input type="text" id="dog_name" name="dog_name" placeholder="Enter the dog's name" required>

            <label for="your_name">Your Name</label>
            <input type="text" id="your_name" name="your_name" placeholder="Enter your name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <button type="reset">Reset</button>
            <button type="submit">Submit</button>
        </form>
    </div>

    <div id="footer">
        <div class="footer-section">
            <div class="section-content about">
                <h2>About Us</h2>
                <p>StraySaver is dedicated to helping stray dogs find loving homes and providing support to pet owners in need. Join us in making a difference.</p>
            </div>
        </div>
        <div class="footer-section">
            <div class="section-content contact">
                <h2>Contact Us</h2>
                <ul>
                    <li><a href="mailto:stray@gmail.com">stray@gmail.com</a></li>
                    <li>074-5694236</li>
                    <li>Dickmens rd, Colombo 3, Sri Lanka</li>
                </ul>
            </div>
        </div>
        <div class="footer-section">
            <div class="section-content social">
                <h2>Follow Us</h2>
                <div class="social-icons">
                    <a href="#"><img src="images/facebook.jpg" alt="Facebook"></a>
                    <a href="#"><img src="images/twitter.jpg" alt="Twitter"></a>
                    <a href="#"><img src="images/instagram.jpg" alt="Instagram"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 StraySaver. All Rights Reserved.
        </div>
    </div>


    <script>
        document.getElementById('adoptDogForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            const form = event.target;
            const formData = new FormData(form);
            const notification = document.getElementById('notification');

            fetch('Adoption.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessage(data.message, 'success');
                    form.reset(); // Clear the form after successful submission
                } else {
                    displayMessage(data.message || 'Failed to save details. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayMessage('Failed to save details. Please try again.', 'error');
            });
        });

        function displayMessage(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';

            // Remove the message after 5 seconds
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }

        function showProfileDetails(profile) {
            // Hide all profile details first
            document.querySelectorAll('.profile-details').forEach(detail => {
                detail.style.display = 'none';
            });

            // Display the selected profile details
            const profileDetails = document.getElementById(`profile-details-${profile.id}`);
            if (profileDetails) {
                profileDetails.style.display = 'block';
            }

            // Automatically fill the dog's name in the form
            document.getElementById('dog_name').value = profile.dog_name;
        }

        let currentIndex = 0;
        const profilesToShow = 5; // Number of profiles to show at once
        const profileList = document.querySelector('.profile-list');
        const profiles = document.querySelectorAll('.profile');
        const totalProfiles = profiles.length;

        document.getElementById('prevBtn').addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + Math.ceil(totalProfiles / profilesToShow)) % Math.ceil(totalProfiles / profilesToShow);
        updateProfileList();
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % Math.ceil(totalProfiles / profilesToShow);
        updateProfileList();
    });

        function updateProfileList() {
            const offset = currentIndex * (100 /Math.ceil(totalProfiles/profilesToShow));
            profileList.style.transform = `translateX(-${offset}%)`;
        }
        document.getElementById('adoptNowButton').addEventListener('click', function() {
    const adoptForm = document.getElementById('adoptForm');
    if (adoptForm) {
        // Scroll to the form
        adoptForm.scrollIntoView({ behavior: 'smooth' });

        // Alternatively, if you want to show the form in a modal or another way, you can do that here
        // adoptForm.style.display = 'block'; // Make sure to handle initial visibility in CSS
    }
});

  
    </script>
</body>
</html>
