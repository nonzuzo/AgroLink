<?php
session_start();
require 'db_connect.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../VIEWS/login.html"); // Redirect to login if not authorized
    exit;
}

// Fetch user details
$userId = $_SESSION['user_id'];
$userName = $_SESSION['name']; //the user's name is stored in session

// Pagination setup
$limit = 5; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch products from the database with pagination
$stmt = $conn->prepare("SELECT * FROM Products WHERE user_id = ? LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $userId, $limit, $offset);
//$stmt = $conn->prepare("SELECT * FROM products LIMIT ?, ?");
//$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

// // Count total products for pagination
// $totalResult = $conn->query("SELECT COUNT(*) FROM products WHERE user_id = ? LIMIT ? OFFSET ?");
// $totalRow = $totalResult->fetch_assoc();
// $totalProducts = $totalRow['total'];
// $totalPages = ceil($totalProducts / $limit);

// Use a prepared statement to count total products for the logged-in user
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM Products WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$totalRow = $result->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/farmer_dashboard.css"> <!--CSS -->
    <title>Farmer Dashboard</title>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="farmer_orders.php">My Orders</a></li>
                <li><a href="farmer_settings.php">Settings</a></li>
                <li><a href="../ACTIONS/logout.php">Logout</a></li> <!-- Link to logout -->
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <h1>Welcome, <?php echo htmlspecialchars($userName); ?></h1>
            <h2>Product Listing</h2>
            <button onclick="window.location.href='../VIEWS/add_new_product.html'">Add New Product</button>

            <!-- Products Table -->
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <!--  -->
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><img src="../../uploads/<?php echo htmlspecialchars($product['image_url']); ?>" 
                            alt="<?php echo htmlspecialchars($product['name']); ?>" width="50"></td>

                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['created_at']); ?></td>
                            <td>
                                <a href="edit_product.php?product_id=<?php echo htmlspecialchars($product['product_id']); ?>">Edit</a> |
                                <a href="delete_product.php?product_id=<?php echo htmlspecialchars($product['product_id']); ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
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

        </div> <!-- End of Main Content Area -->

    </div> <!-- End of Container -->

</body>

<?php
// Close the database connection
$conn->close();
?>