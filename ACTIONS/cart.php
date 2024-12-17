<?php
session_start();
include 'db_connect.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}



// Get the user's cart items from the database
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart Total</title>
    <link rel="stylesheet" href="../CSS/cart.css">
   
</head>
<body>
    <div class="cart-container">
        <h1>Your Cart Total</h1>
        <div id="cart" class="cart-item-list">
            <!-- cart.js populates this section -->
        </div>

        <div class="total-summary">
            <h2>Grand Total:</h2>
            <span id="grand-total">$<?php echo number_format($grandTotal, 2); ?></span>
        </div>

        <div class="checkout-actions">
            <a href="checkout.php" id="checkoutBtn" class="checkout-button">Proceed to Checkout</a>
            <a href="product_listing.php" class="continue-shopping">Continue Shopping</a>
        </div>
    </div>
    <script src="../JS/cart.js"></script>  
</body>
</html>

<?php
$conn->close();
?>