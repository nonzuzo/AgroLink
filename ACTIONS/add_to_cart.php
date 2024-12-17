<?php
session_start();

// Include database connection
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: ../VIEWS/login.html");
    exit();
}

// User is logged in, proceed with adding item to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID
    $product_id = intval($_POST['product_id']);
    
    // Check if the product is already in the cart
    $stmt = $conn->prepare("SELECT quantity FROM Cart_Items WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Product exists in cart, update quantity
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + 1; // Increment quantity by 1

        $update_stmt = $conn->prepare("UPDATE Cart_Items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
        $update_stmt->execute();
        $update_stmt->close();
    } else {
        // Product does not exist in cart, insert new record
        $insert_stmt = $conn->prepare("INSERT INTO Cart_Items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $quantity = 1; // Default quantity
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
        $insert_stmt->close();
    }

    // Set a success message
    $_SESSION['message'] = "Item added successfully!";
    
    // Redirect back to product listings or wherever appropriate
    header("Location: product_listing.php");
    exit();
}
?>