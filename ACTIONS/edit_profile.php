<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['email'])) {
    header("Location:../ACTIONS/login.php");
    exit();
}

$email = $_SESSION['email'];

// Initialize error messages
$nameError = $locationError = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);

    // Validate inputs
    if (empty($name)) {
        $nameError = "Name is required.";
    }
    if (empty($location)) {
        $locationError = "Location is required.";
    }

    // If no errors, update the profile
    if (empty($nameError) && empty($locationError)) {
        $stmt = $conn->prepare("UPDATE Users SET name=?, location=? WHERE email=?");
        $stmt->bind_param("sss", $name, $location, $email);
        if ($stmt->execute()) {
            echo "Profile updated successfully!";
        } else {
            echo "Error updating profile: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    } else {
        // Return error messages
        echo htmlspecialchars($nameError) ?: htmlspecialchars($locationError);
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>