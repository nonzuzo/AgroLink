<?php
session_start();
require 'db_connect.php'; // Include your database connection

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id'])) {
    header("Location: ../VIEWS/login.html"); // Redirect to login if not authorized
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];

// Pagination setup
$limit = 5; // Number of orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch farmer's products first
$stmt = $conn->prepare("SELECT product_id FROM Products WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$productResult = $stmt->get_result();
$farmerProductIds = [];
while ($row = $productResult->fetch_assoc()) {
    $farmerProductIds[] = $row['product_id'];
}

// If farmer has no products, set to an impossible value to return no results
$productIdList = !empty($farmerProductIds) ? implode(',', $farmerProductIds) : '0';

// Fetch orders that contain the farmer's products
$orderQuery = "
    SELECT DISTINCT 
        o.order_id, 
        o.total_amount, 
        o.order_date, 
        o.status, 
        u.name as buyer_name
    FROM Orders o
    JOIN Order_Items oi ON o.order_id = oi.order_id
    JOIN Users u ON o.buyer_id = u.user_id
    WHERE oi.product_id IN ($productIdList)
    ORDER BY o.order_date DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($orderQuery);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Count total orders
$countQuery = "
    SELECT COUNT(DISTINCT o.order_id) as total
    FROM Orders o
    JOIN Order_Items oi ON o.order_id = oi.order_id
    WHERE oi.product_id IN ($productIdList)
";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute();
$totalResult = $countStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalOrders = $totalRow['total'];
$totalPages = ceil($totalOrders / $limit);

// Function to get order items
function getOrderItems($conn, $orderId, $farmerProductIds) {
    $productIdList = implode(',', $farmerProductIds);
    $stmt = $conn->prepare("
        SELECT 
            p.name as product_name, 
            oi.quantity, 
            oi.price 
        FROM Order_Items oi
        JOIN Products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ? AND oi.product_id IN ($productIdList)
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/farmer_dashboard.css">
    <title>Farmer Orders</title>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="farmer_dashboard.php">Dashboard</a></li>
                <li><a href="farmer_settings.php">Settings</a></li>
                <li><a href="../ACTIONS/logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <h1>My Orders</h1>

            <?php if (empty($orders)): ?>
                <p>No orders found for your products.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Buyer</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td>
                                    <a href="#" onclick="toggleOrderDetails(<?php echo $order['order_id']; ?>)">
                                        View Items
                                    </a>
                                </td>
                            </tr>
                            <tr id="order-details-<?php echo $order['order_id']; ?>" style="display:none;">
                                <td colspan="6">
                                    <table>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                        </tr>
                                        <?php 
                                        $orderItems = getOrderItems($conn, $order['order_id'], $farmerProductIds);
                                        foreach ($orderItems as $item): 
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo ($i === $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleOrderDetails(orderId) {
        var detailsRow = document.getElementById('order-details-' + orderId);
        detailsRow.style.display = detailsRow.style.display === 'none' ? 'table-row' : 'none';
    }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>