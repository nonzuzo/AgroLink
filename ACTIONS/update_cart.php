<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
   header("HTTP/1.1 403 Forbidden");
   exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $data = json_decode(file_get_contents("php://input"), true);
   
   $cart_item_id = intval($data['cart_item_id']);
   $quantity = intval($data['quantity']);
   
   // Update quantity in Cart_Items table for this user and product ID.
   $stmt = $conn->prepare("UPDATE Cart_Items SET quantity = ? WHERE user_id = ? AND cart_item_id = ?");
   $stmt->bind_param("iii", $quantity, $_SESSION['user_id'], $cart_item_id);
   
   if ($stmt->execute()) {
       echo json_encode(['success' => true]);
   } else {
       echo json_encode(['success' => false]);
   }

   $stmt->close();
}
$conn->close();
?>