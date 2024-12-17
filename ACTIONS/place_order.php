<?php
session_start();
include 'db_connect.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$total = $_POST['total'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$location = $_POST['location'];
$payment_method = $_POST['payment_method'];

// Banking details
$card_number = isset($_POST['card_number']) ? $_POST['card_number'] : null;
$card_expiration = isset($_POST['card_expiration']) ? $_POST['card_expiration'] : null;
$card_cvv = isset($_POST['card_cvv']) ? $_POST['card_cvv'] : null;

// Begin transaction
$conn->begin_transaction();

try {
    // Insert order into Orders table with additional details
    $stmt = $conn->prepare("
        INSERT INTO Orders (buyer_id, total_amount, location, payment_method, phone, card_number, card_expiration, card_cvv) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("idssssss", $user_id, $total, $location, $payment_method, $phone, $card_number, $card_expiration, $card_cvv);
    $stmt->execute();
    
    // Get the last inserted order ID
    $orderId = $stmt->insert_id;

    // Fetch cart items for this user
    $stmtCart = $conn->prepare("
        SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.price 
        FROM Cart_Items ci 
        JOIN Products p ON ci.product_id = p.product_id 
        WHERE ci.user_id = ?
    ");
    $stmtCart->bind_param("i", $user_id);
    $stmtCart->execute();
    $resultCart = $stmtCart->get_result();

    while ($row = $resultCart->fetch_assoc()) {
        // Insert each cart item into Order_Items table
       
       // Make sure you have created this table beforehand.
       
       // Insert into Order_Items table
       $stmtOrderItem = $conn->prepare("INSERT INTO Order_Items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
       $stmtOrderItem->bind_param("iiid", $orderId, $row['product_id'], $row['quantity'], $row['price']);
       $stmtOrderItem->execute();
       
       //remove item from Cart_Items table after placing order
       //  check here if theres an issue
        $deleteStmt = $conn->prepare("DELETE FROM Cart_Items WHERE cart_item_id = ?");
        $deleteStmt->bind_param("i", $row['cart_item_id']);
       $deleteStmt->execute();
   }

   // Commit transaction
   $conn->commit();

   // Redirect to a success page or display a success message
   header("Location: order_success.php?order_id=$orderId");
} catch (Exception $e) {
   // Rollback transaction in case of error
   $conn->rollback();
   
   // Handle error (e.g., log it and redirect to an error page)
   echo "Error placing order: " . htmlspecialchars($e->getMessage());
}

$conn->close();
?>