<?php
session_start(); // Start the session to check if user is logged in

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit();
}

// Include the database connection
include 'db_connect.php';

// Get the user ID from session
$user_id = $_SESSION['user_id'];

// Get the data from the request
$json = file_get_contents("php://input");
$data = json_decode($json);
$itemId = $data->itemId;

// Prepare and execute the delete query
$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $itemId, $user_id);
$stmt->execute();

// Check if the deletion was successful
if ($stmt->affected_rows > 0) {
    echo json_encode([
        'success' => true,
        'message' => 'Item removed from cart'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to remove item'
    ]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
