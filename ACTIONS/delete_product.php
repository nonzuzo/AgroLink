<?php
session_start();
include 'db_connect.php';

var_dump($_GET);

// Check if product_id is passed via GET and validate it
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $user_id = $_SESSION['user_id']; // User ID from session

    // Fetch the product details from the database
    $stmt = $conn->prepare("SELECT image_url FROM Products WHERE product_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If product exists and belongs to the current user
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $image_url = $product['image_url'];

        // Optionally delete the image file (if it exists)
        if (file_exists($image_url)) {
            unlink($image_url); // Deletes the file from the server
        }

        // Now delete the product record from the database
        $stmtDelete = $conn->prepare("DELETE FROM Products WHERE product_id = ? AND user_id = ?");
        $stmtDelete->bind_param("ii", $product_id, $user_id);

        if ($stmtDelete->execute()) {
            // Redirect after successful deletion
            header("Location: farmer_dashboard.php?message=Product deleted successfully.");
            exit();
        } else {
            echo json_encode(["success" => false, "message" => "Error deleting product: " . htmlspecialchars($stmtDelete->error)]);
        }

        $stmtDelete->close();
    } else {
        // If product doesn't exist or the user is unauthorized
        echo json_encode(["success" => false, "message" => "Product not found or access denied."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "No product selected for deletion."]);
}

$conn->close();
?>
