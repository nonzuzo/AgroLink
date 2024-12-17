<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // User ID from session
$userName = $_SESSION['user_name']; // Assuming the user's name is stored in the session

// Initialize variables for the insights
$totalProducts = 0;
$categories = [];
$totalRevenue = 0;
$recentProducts = [];

// Query 1: Total number of products
$stmtTotalProducts = $conn->prepare("SELECT COUNT(*) as total FROM Products WHERE user_id = ?");
$stmtTotalProducts->bind_param("i", $user_id);
$stmtTotalProducts->execute();
$result = $stmtTotalProducts->get_result();
$totalProducts = $result->fetch_assoc()['total'];
$stmtTotalProducts->close();

// Query 2: Product categories breakdown
$stmtCategories = $conn->prepare("SELECT category_id, COUNT(*) as count FROM Products WHERE user_id = ? GROUP BY category_id");
$stmtCategories->bind_param("i", $user_id);
$stmtCategories->execute();
$resultCategories = $stmtCategories->get_result();
while ($row = $resultCategories->fetch_assoc()) {
    $categories[] = $row;
}
$stmtCategories->close();

// Query 3: Total revenue from all products (price * quantity)
$stmtTotalRevenue = $conn->prepare("SELECT SUM(price * quantity) as revenue FROM Products WHERE user_id = ?");
$stmtTotalRevenue->bind_param("i", $user_id);
$stmtTotalRevenue->execute();
$resultRevenue = $stmtTotalRevenue->get_result();
$totalRevenue = $resultRevenue->fetch_assoc()['revenue'];
$stmtTotalRevenue->close();

// Query 4: Recent products added
$stmtRecentProducts = $conn->prepare("SELECT name, created_at FROM Products WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmtRecentProducts->bind_param("i", $user_id);
$stmtRecentProducts->execute();
$resultRecentProducts = $stmtRecentProducts->get_result();
while ($row = $resultRecentProducts->fetch_assoc()) {
    $recentProducts[] = $row;
}
$stmtRecentProducts->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Insights</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="../CSS/insights.css">
</head>
<body>
    <!-- Sidebar Section -->
    <div class="sidebar">
        <h2>Farmer Dashboard</h2>
        <a href="farmer_dashboard.php">Go to Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="container">
            <h1>Dashboard Insights</h1>
            <div class="welcome-message">
                <p>Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
            </div>

            <div class="insight-cards">
                <!-- Total Products -->
                <div class="card">
                    <h3>Total Products</h3>
                    <p><?php echo $totalProducts; ?> Products</p>
                </div>

                <!-- Product Categories Breakdown -->
                <div class="card">
                    <h3>Product Categories Breakdown</h3>
                    <?php if (count($categories) > 0): ?>
                        <ul>
                            <?php foreach ($categories as $category): ?>
                                <li>Category <?php echo $category['category_id']; ?>: <?php echo $category['count']; ?> products</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No products in any categories.</p>
                    <?php endif; ?>
                </div>

                <!-- Total Revenue -->
                <div class="card">
                    <h3>Total Revenue</h3>
                    <p>$<?php echo number_format($totalRevenue, 2); ?></p>
                </div>

                <!-- Recent Products -->
                <div class="card">
                    <h3>Recent Products Added</h3>
                    <?php if (count($recentProducts) > 0): ?>
                        <ul>
                            <?php foreach ($recentProducts as $product): ?>
                                <li><?php echo $product['name']; ?> (Added on: <?php echo date("F j, Y", strtotime($product['created_at'])); ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No recent products added.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
