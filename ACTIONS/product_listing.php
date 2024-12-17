<?php
session_start();
include 'db_connect.php';

// Display success message if it exists
if (isset($_SESSION['message'])) {
    echo "<div class='success-message'>" . htmlspecialchars($_SESSION['message']) . "</div>";
    unset($_SESSION['message']); // Clear message after displaying it
}

// Fetch products from the database
$sql = "SELECT Products.product_id, Products.name AS product_name, Products.price, 
               Products.image_url, Products.location, Categories.name AS category_name 
        FROM Products 
        INNER JOIN Categories ON Products.category_id = Categories.category_id
        INNER JOIN Users ON Products.user_id = Users.user_id
        WHERE Users.status = 'approved' AND Users.user_type = 'farmer'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listings</title>
    <link rel="stylesheet" href="../CSS/product_listing.css"> <!-- Link to CSS -->
    <script src="../JS/product_listing.js" defer></script> <!-- Link to JavaScript -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
      <!-- Cart Button positioned at the top right -->
    <div class="cart-button">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="../VIEWS/login.html" style="color: white;">🛒 Cart</a>
        <?php else: ?>
            <a href="cart.php" style="color: white;">🛒 Cart</a>
        <?php endif; ?>
    </div>
    
    <div class="listings-container">
        <!-- Page Title -->
        <h1>Product Listings</h1>

        <!-- Filter and Sort Sections -->
        <div class="filter-section">
            <input type="text" id="searchBar" placeholder="Search for products..." />
            <select id="categoryFilter">
                <option value="">All Categories</option>
                <option value="fruits">Fruits</option>
                <option value="vegetables">Vegetables</option>
                <option value="dairy">Dairy</option>
                <option value="grains">Grains</option>
            </select>
            <input type="number" id="minPrice" placeholder="Min Price" min="0" />
            <input type="number" id="maxPrice" placeholder="Max Price" min="0" />
            <button id="filterBtn">Filter</button>
        </div>

        <div class="sort-section">
            <label for="sortBy">Sort By:</label>
            <select id="sortBy">
                <option value="price">Price</option>
                <option value="location">Location</option>
                <option value="date">Date Listed</option>
            </select>
        </div>

        <!-- Product Listings -->
        <div class="listings-grid" id="listingsGrid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-item" data-category="<?= strtolower($row['category_name']) ?>">
                        <!-- Update image URL to include uploads directory -->
                        <img src="<?= htmlspecialchars('../../uploads/' . $row['image_url']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" />
                        <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                        <p>Price: $<?= htmlspecialchars($row['price']) ?></p>
                        <p>Location: <?= htmlspecialchars($row['location']) ?></p>
                        <!-- Add to Cart Form -->
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                            <button type="submit" class="cart-icon">Add to Cart 🛒</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>