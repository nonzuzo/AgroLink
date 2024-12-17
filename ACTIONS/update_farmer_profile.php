<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    
    if (!empty($name) && !empty($email) && !empty($location)) {
        $stmt = $conn->prepare("UPDATE Users SET name=?, email=? WHERE email=?");
        $stmt->bind_param("sss", $name, $email, $_SESSION['email']);
        
        if ($stmt->execute()) {
            $_SESSION['name'] = $name; // Update session variable
            echo "Profile updated successfully!";
        } else {
            echo "Error updating profile: " . htmlspecialchars($stmt->error);
        }
        
        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}

$conn->close();
?>