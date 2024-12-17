<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../VIEWS/login.html");
    exit();
}

// Include database connection
include 'db_connect.php';
global $conn;

// Fetch all products
$productQuery = "SELECT product_id, name, status FROM Products";
$productResult = $conn->query($productQuery);
$products = $productResult->fetch_all(MYSQLI_ASSOC);

// Handle approval or rejection
if (isset($_GET['action']) && isset($_GET['product_id'])) {
    $action = $_GET['action'];
    $product_id = $_GET['product_id'];

    if ($action === 'approve') {
        // Update the product status to 'approved'
        $updateQuery = "UPDATE Products SET status = 'approved' WHERE product_id = ?";
        $_SESSION['status_message'] = 'Product has been successfully approved.';
    } elseif ($action === 'reject') {
        // Update the product status to 'rejected'
        $updateQuery = "UPDATE Products SET status = 'rejected' WHERE product_id = ?";
        $_SESSION['status_message'] = 'Product has been successfully rejected.';
    }

    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    } else {
        echo "Error: " . $conn->error;
    }

    // Redirect to the same page to show the message
    header("Location: admin_product_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
    <style>
        /* CSS for displaying the status message */
        .status-message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="farmer_approvals.php">Farmer Approvals</a></li>
                <li><a href="admin_product_management.php">Product Management</a></li>
                <!-- <li><a href="admin_order_management.php">Order Management</a></li> -->
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h1>Product Management</h1>

            <?php
            // Display status message if it exists
            if (isset($_SESSION['status_message'])) {
                echo "<p class='status-message'>" . $_SESSION['status_message'] . "</p>";
                unset($_SESSION['status_message']); // Clear the message after displaying it
            }
            ?>

            <table class="data-table">
                <tr>
                     
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($products as $product): ?>
                    <tr> 
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['status']); ?></td>
                        <td>
                            <a href="admin_product_management.php?action=approve&product_id=<?php echo urlencode($product['product_id']); ?>">Approve</a> |
                            <a href="admin_product_management.php?action=reject&product_id=<?php echo urlencode($product['product_id']); ?>">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
