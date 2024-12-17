<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connect.php';
global $conn;

// Fetch all orders
$orderQuery = "SELECT order_id, buyer_id, status FROM Orders";
$orderResult = $conn->query($orderQuery);
$orders = $orderResult->fetch_all(MYSQLI_ASSOC);

// Change order status
if (isset($_GET['action']) && isset($_GET['order_id'])) {
    $action = $_GET['action'];
    $order_id = $_GET['order_id'];

    if ($action === 'complete') {
        $updateQuery = "UPDATE Orders SET status = 'Completed' WHERE order_id = ?";
    } elseif ($action === 'cancel') {
        $updateQuery = "UPDATE Orders SET status = 'Cancelled' WHERE order_id = ?";
    }

    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        header("Location: manage_orders.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="farmer_approvals.php">Farmer Approvals</a></li>
                <li><a href="manage_products.php">Product Management</a></li>
                <li><a href="manage_orders.php">Order Management</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h1>Order Management</h1>
            <table class="data-table">
                <tr>
                    <th>Order ID</th>
                    <th>Buyer ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['buyer_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <a href="manage_orders.php?action=complete&order_id=<?php echo urlencode($order['order_id']); ?>">Complete</a> |
                            <a href="manage_orders.php?action=cancel&order_id=<?php echo urlencode($order['order_id']); ?>">Cancel</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
