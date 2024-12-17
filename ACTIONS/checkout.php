<?php
session_start();
include 'db_connect.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cartItems = [];
$grandTotal = 0;

// Fetch user details
$stmtUser = $conn->prepare("SELECT name, email FROM Users WHERE user_id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$userDetails = $userResult->fetch_assoc();

$stmtUser->close();

// Fetch cart items for the logged-in user
$stmtCart = $conn->prepare("
SELECT ci.cart_item_id, p.product_id, p.name AS product_name, p.price, ci.quantity, p.image_url 
    FROM Cart_Items ci 
    JOIN Products p ON ci.product_id = p.product_id 
    WHERE ci.user_id = ?
");
$stmtCart->bind_param("i", $user_id);
$stmtCart->execute();
$resultCart = $stmtCart->get_result();

while ($row = $resultCart->fetch_assoc()) {
    $cartItems[] = $row;
    $grandTotal += $row['price'] * $row['quantity']; // Calculate grand total
}

$stmtCart->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        /* Add your existing styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .checkout-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        h2 {
            color: #555;
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .cart-item-list {
            margin-top: 20px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .cart-item img {
            width: 80px; 
            height: auto; 
            margin-right: 15px; 
        }
        .cart-item-details {
            flex-grow: 1; 
        }
        .total-summary {
            font-size: 18px; 
            font-weight: bold; 
            margin-top: 20px; 
        }
        .checkout-button {
            background-color: #5cb85c; /* Bootstrap success color */
            color: white; 
            padding: 10px; 
            border-radius: 5px; 
            border: none; 
            cursor: pointer; 
            width: 100%; 
        }
        .checkout-button:hover {
            background-color: #4cae4c; /* Darker shade on hover */
        }
        .continue-shopping {
            display: block; 
            text-align: center; 
            margin-top: 20px; 
            text-decoration: none; 
            color: #007bff; /* Bootstrap primary color */
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout</h1>

        <form action="place_order.php" method="POST">
            <div class="user-details">
                <h2>Your Details</h2>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userDetails['name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userDetails['email']); ?>" required>

                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required>

                <label for="location">Delivery Location:</label>
                <input type="text" id="location" name="location" required>

                <label for="payment_method">Payment Method:</label>
                <select id="payment_method" name="payment_method" required onchange="toggleBankingDetails()">
                    <option value="" disabled selected>Select a payment method</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>

                </select>

                <!-- Banking details section -->
                <div id="banking-details" style="display:none;">
                    <h3>Banking Details</h3>
                    <label for="card_number">Card Number:</label>
                    <input type="text" id="card_number" name="card_number" required>

                    <label for="card_expiration">Expiration Date (YYYY-MM-DD):</label>
                    <input type="date" id="card_expiration" name="card_expiration" required>

                    <label for="card_cvv">CVV:</label>
                    <input type="text" id="card_cvv" name="card_cvv" required >
                </div>
            </div>

            <div id="cart" class="cart-item-list">
                <?php if (empty($cartItems)): ?>
                    <p>Your cart is empty.</p>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars('../uploads/' . $item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <div class="cart-item-details">
                                <p><?php echo htmlspecialchars($item['product_name']); ?> - Quantity: <?php echo $item['quantity']; ?></p>
                                <p>Price: $<span class="item-price"><?php echo number_format($item['price'], 2); ?></span></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="total-summary">
                Grand Total:
                <span id="grand-total">$<?php echo number_format($grandTotal, 2); ?></span>
            </div>

            <!-- Hidden input to send total amount -->
            <input type="hidden" name="total" value="<?php echo $grandTotal; ?>">
            
            <button type="submit" class="checkout-button">Place Order</button>
        </form>

        <a href="product_listing.php" class="continue-shopping">Continue Shopping</a>
    </div>

    <script>
        function toggleBankingDetails() {
            const paymentMethod = document.getElementById('payment_method').value;
            const bankingDetails = document.getElementById('banking-details');
            
            if (paymentMethod === 'Credit Card' || paymentMethod === 'Debit Card') {
                bankingDetails.style.display = 'block';
            } else {
                bankingDetails.style.display = 'none';
                // Clear banking details when not needed
                document.getElementById('card_number').value = '';
                document.getElementById('card_expiration').value = '';
                document.getElementById('card_cvv').value = '';
            }
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>