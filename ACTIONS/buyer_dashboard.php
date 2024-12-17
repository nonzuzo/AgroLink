<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../VIEWS/login.html"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // User ID from session
$userName = $_SESSION['name']; // User name from session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Dashboard</title>
    <link rel="stylesheet" href="../CSS/buyer_dashboard.css">
</head>
<body>
    <!-- Sidebar Section -->
    <div class="sidebar">
        <h2>Buyer Dashboard</h2>
        <a href="buyer_dashboard.php">Dashboard</a>
        <a href="product_listing.php">Products</a>
        <a href="cart.php">Cart</a>
        <a href="buyer_settings.php">Account Settings</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="container">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="card-header">
                    <h1>Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
                </div>
                <div class="card-body">
                    <p>Explore products, manage your cart, and track your orders from your dashboard.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
