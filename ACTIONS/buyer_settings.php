<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../VIEWS/login.html");
    exit();
}

$user_id = $_SESSION['user_id']; // User ID from session
$userName = $_SESSION['name']; // User's name from session

// Handle form submission to update the name
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    // Validate the new name
    if (empty($name)) {
        $error_message = "Please enter a valid name.";
    } else {
        // Update the name in the database
        $stmt = $conn->prepare("UPDATE Users SET name = ? WHERE user_id = ?");
        $stmt->bind_param("si", $name, $user_id);

        if ($stmt->execute()) {
            // Update session variable
            $userName = $_SESSION['name'] = $name;
            $success_message = "Your name has been updated successfully!";
        } else {
            $error_message = "There was an error updating your name. Please try again.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="../CSS/settings.css">
</head>
<body>
    <div class="sidebar">
        <h2>Buyer Dashboard</h2>
        <a href="buyer_dashboard.php">Go to Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <div class="container">
            <h1>Update Profile</h1>
            
            <!-- Display success or error message -->
            <?php if (isset($success_message)): ?>
                <div class="alert success">
                    <?= htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert error">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Profile Update Form -->
            <form method="POST" action="buyer_settings.php">
                <label for="new_name">New Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($userName); ?>" required>
                <button type="submit">Update Name</button>
            </form>
        </div>
    </div>
</body>
</html>
