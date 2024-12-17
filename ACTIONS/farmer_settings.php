<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../VIEWS/login.html"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // User ID from session
$userName = $_SESSION['name']; // Assuming the user's name is stored in the session

// Handle form submission to update the name
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    
    // Validate the new name (optional)
    if (!empty($name)) {
        // Update name in the database
        $stmt = $conn->prepare("UPDATE Users SET name = ? WHERE user_id = ?");
        $stmt->bind_param("si", $name, $user_id);
        
        if ($stmt->execute()) {
            // Update the session with the new name
            $userName = $_SESSION['name'] = $name;
            $success_message = "Your name has been updated successfully!";
        } else {
            $error_message = "There was an error updating your name. Please try again.";
        }

        $stmt->close();
    } else {
        $error_message = "Please enter a valid name.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="../CSS/settings.css">
</head>
<body>
    <!-- Sidebar Section -->
    <div class="sidebar">
        <h2>Farmer Dashboard</h2>
        <a href="farmer_dashboard.php">Go to Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="container">
            <h1>Update Profile</h1>
            
            <!-- Display success or error message -->
            <?php if (isset($success_message)): ?>
                <div class="alert success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Profile Update Form -->
            <form action="farmer_settings.php" method="POST">
                <label for="name">New Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>" required>
                <button type="submit">Update Name</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
