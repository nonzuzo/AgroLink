<?php
session_start();
include 'db_connect.php';

// Check if the product_id is passed via GET
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    
    // Fetch the current product details from the database
    $stmt = $conn->prepare("SELECT name, description, price, quantity, location, category_id, image_url FROM Products WHERE product_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $product_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        // Fetch categories for dropdown
        $categoriesResult = $conn->query("SELECT category_id, name FROM Categories");
    } else {
        // Product not found or unauthorized access
        echo "Product not found or access denied.";
        exit();
    }
} else {
    echo "No product selected.";
    exit();
}

// Handle form submission when POST request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $category = intval($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $location = trim($_POST['location']);
    $user_id = $_SESSION['user_id'];

    // Validate form data
    if (!empty($name) && !empty($category) && !empty($description) && $price > 0 && $quantity > 0 && !empty($location)) {
        
        // Handle image upload
        // $image_url = $product['image_url']; // Keep current image if no new image is uploaded
        // if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
        //     $imageTmpPath = $_FILES['imageUpload']['tmp_name'];
        //     $imageName = $_FILES['imageUpload']['name'];
        //     $imageSize = $_FILES['imageUpload']['size'];
        //     $imageType = $_FILES['imageUpload']['type'];

        //     // Define allowed file types and size limit (2MB)
        //     $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        //     $maxSize = 2 * 1024 * 1024; // 2MB

        //     // MIME type validation
        //     $fileMimeType = mime_content_type($imageTmpPath);
        //     if (in_array($fileMimeType, $allowedTypes) && $imageSize <= $maxSize) {
        //         $uploadDir = '../uploads/';
        //         if (!is_dir($uploadDir)) {
        //             mkdir($uploadDir, 0755, true);
        //         }

        //         $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        //         $newImageName = uniqid() . '.' . $fileExtension;
        //         $uploadFilePath = $uploadDir . $newImageName;

        //         if (move_uploaded_file($imageTmpPath, $uploadFilePath)) {
        //             $image_url = $uploadFilePath;
        //         } else {
        //             echo json_encode(["success" => false, "message" => "Error moving uploaded file."]);
        //             exit();
        //         }
        //     } else {
        //         echo json_encode(["success" => false, "message" => "Invalid file type or size exceeded."]);
        //         exit();
        //     }
        // }
        // Handle image upload
$image_url = $product['image_url']; // Preserve the existing image URL by default
if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
    $imageTmpPath = $_FILES['imageUpload']['tmp_name'];
    $imageName = $_FILES['imageUpload']['name'];
    $imageSize = $_FILES['imageUpload']['size'];
    $imageType = $_FILES['imageUpload']['type'];

    // Define allowed file types and size limit (2MB)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // MIME type validation
    $fileMimeType = mime_content_type($imageTmpPath);
    if (in_array($fileMimeType, $allowedTypes) && $imageSize <= $maxSize) {
        $uploadDir = '../../uploads/';//changed here
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $newImageName = uniqid() . '.' . $fileExtension;
        $uploadFilePath = $uploadDir . $newImageName;

        if (move_uploaded_file($imageTmpPath, $uploadFilePath)) {
            $image_url = $uploadFilePath;
        } else {
            echo json_encode(["success" => false, "message" => "Error moving uploaded file."]);
            exit();
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid file type or size exceeded."]);
        exit();
    }
}

         // Update product details
         $stmtUpdate = $conn->prepare("UPDATE Products SET name=?, description=?, price=?, quantity=?, location=?, category_id=?, image_url=? WHERE product_id=? AND user_id=?");
         $stmtUpdate->bind_param("ssdisisii", $name, $description, $price, $quantity, $location, $category, $image_url, $product_id, $user_id);

        // // Update product details
        // $stmtUpdate = $conn->prepare("UPDATE Products SET name=?, description=?, price=?, quantity=?, location=?, category_id=?, image_url=? WHERE product_id=? AND user_id=?");
        // $stmtUpdate->bind_param("ssdisisii", $name, $description, $price, $quantity, $location, $category, $newImageName, $product_id, $user_id);

        if ($stmtUpdate->execute()) {
             
            echo json_encode(["success" => true, "message" => "Product updated successfully!"]);
            header("Location: farmer_dashboard.php");
            
        } else {
            echo json_encode(["success" => false, "message" => "Error updating product: " . htmlspecialchars($stmtUpdate->error)]);
        }

        $stmtUpdate->close();
    } else {
        echo json_encode(["success" => false, "message" => "All fields are required and must be valid."]);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../CSS/edit_product.css">
</head>
<body>

<h2>Edit Product</h2>
<form action="edit_product.php?product_id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br><br>

    <label for="category">Category:</label>
    <select name="category" id="category" required>
        <?php while ($category = $categoriesResult->fetch_assoc()) { ?>
            <option value="<?php echo $category['category_id']; ?>"
                <?php echo $category['category_id'] == $product['category_id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
        <?php } ?>
    </select><br><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

    <label for="price">Price:</label>
    <input type="number" step="0.01" id="price" name="price" value="<?php echo $product['price']; ?>" required><br><br>

    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" value="<?php echo $product['quantity']; ?>" required><br><br>

    <label for="location">Location:</label>
    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($product['location']); ?>" required><br><br>

    <label for="imageUpload">Upload New Image (Optional):</label>
    <input type="file" name="imageUpload" id="imageUpload"><br><br>

    <button type="submit">Update Product</button>
</form>

</body>
</html>

<?php $stmt->close(); ?>
