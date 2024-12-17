<?php
session_start();
include 'db_connect.php'; // Include your database connection file

if (!isset($_SESSION['user_id'])) {
   header("HTTP/1.1 403 Forbidden");
   exit();
}

$user_id = $_SESSION['user_id'];
$cartItems = [];

// Fetch cart items for the logged-in user
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, p.product_id, p.name AS product_name, p.price, ci.quantity, p.image_url 
    FROM Cart_Items ci 
    JOIN Products p ON ci.product_id = p.product_id 
    WHERE ci.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
   $cartItems[] = $row; // Add each item to the cartItems array
}

$stmt->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($cartItems);

$conn->close();
?>