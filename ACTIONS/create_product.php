<?php
session_start();
include 'db_connect.php';

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['productName']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $location = trim($_POST['location']);
    $user_id = $_SESSION['user_id'];



    //$uploadDir = __DIR__ . '/uploads/';

    // Validate form data
    if (!empty($name) && !empty($category) && !empty($description) && $price > 0 && $quantity > 0 && !empty($location)) {
        // Handle image upload
        if (isset($_POST)) {
            $file_name = $_FILES['imageUpload']['name'];
            $tempname = $_FILES['imageUpload']['tmp_name'];
            $folder = '../../uploads/' . $file_name;

            // // Ensure uploads directory exists
            // if (!is_dir('../uploads')) {
            //     mkdir('../uploads', 0777, true);
            // }


            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($tempname);
            if (!in_array($fileType, $allowedTypes)) {
                echo "<h2>Invalid file type. Only JPEG, PNG, and GIF are allowed.</h2>";
                exit;
            }
            if (move_uploaded_file($tempname, $folder)) {
                // Get category ID
                $stmtCategoryId = $conn->prepare("SELECT category_id FROM Categories WHERE name = ?");
                $stmtCategoryId->bind_param("s", $category);
                $stmtCategoryId->execute();
                $resultCategoryId = $stmtCategoryId->get_result();



                if ($rowCategoryId = $resultCategoryId->fetch_assoc()) {
                    $category_id = $rowCategoryId['category_id'];

                    // Insert product details into database
                    $stmtInsertProduct = $conn->prepare(
                        "INSERT INTO Products (user_id, name, description, price, quantity, category_id, location, image_url) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                    );
                    $stmtInsertProduct->bind_param(
                        "issiiiss",
                        $user_id,
                        $name,
                        $description,
                        $price,
                        $quantity,
                        $category_id,
                        $location,
                        $file_name
                    );
                    if ($stmtInsertProduct->execute()) {
                        echo "<h2>Product added successfully!</h2>";
                        header("Location: farmer_dashboard.php");
                    } else {
                        echo "<h2>Error adding product: " . htmlspecialchars($stmtInsertProduct->error) . "</h2>";
                    }

                    $stmtInsertProduct->close();
                } else {
                    echo "<h2>Invalid category selected.</h2>";
                }
                $stmtCategoryId->close();
            } else {
                echo "<h2>Failed to upload file</h2>";
            }
        } else {
            echo "<h2>No file uploaded</h2>";
        }
    } else {
        echo "<h2>All fields are required and must be valid.</h2>";
    }
    $conn->close();
}








//             //$imageName = $_FILES['imageUpload']['name'];
//             //$imageSize = $_FILES['imageUpload']['size'];
//             //$imageType = $_FILES['imageUpload']['type'];

//             // Define allowed file types and size limit (2MB)
//             // $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
//             // $maxSize = 2 * 1024 * 1024; // 2MB

//             // MIME type validation
//             // $fileMimeType = mime_content_type($imageTmpPath);


//             // Check if directory exists, if not try to create it
//             // if (!is_dir($uploadDir)) {
//             //     try {
//             //         mkdir($uploadDir, 777, true);
//             //     } catch (Exception $e) {
//             //         error_log("Failed to create directory: " . $e->getMessage());
//             //         echo json_encode(["success" => false, "message" => "Failed to create upload directory"]);
//             //         exit;
//             //     }
//             //}


//              if (in_array($fileMimeType, $allowedTypes) && $imageSize <= $maxSize) {
//                 // Ensure upload directory exists and is writable
//                 $uploadDir = __DIR__ . '/uploads/';  // Use absolute path
//                 if (!is_dir($uploadDir)) {
//                     mkdir($uploadDir, 777, true);
//                 }

//                 // Create unique filename to prevent overwriting
//                 $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
//                 $newImageName = uniqid() . '_' . time() . '.' . $fileExtension;
//                 $uploadFilePath = $uploadDir . $newImageName;

//                 // Log upload attempt details
//                 error_log("Upload attempt - Temp path: $imageTmpPath");
//                 error_log("Upload attempt - Destination: $uploadFilePath");

//                 // Check if temp file exists and is readable
//                 if (!is_readable($imageTmpPath)) {
//                     echo json_encode(["success" => false, "message" => "Cannot read temporary file"]);
//                     exit;
//                 }

//                 // Check if upload directory is writable
//                 if (!is_writable($uploadDir)) {
//                     echo json_encode(["success" => false, "message" => "Upload directory is not writable"]);
//                     exit;
//                 }

//                 // Attempt to move the file
//                 if (move_uploaded_file($imageTmpPath, $uploadFilePath)) {
//                     // Store the relative path in the database
//                     $databaseFilePath = 'uploads/' . $newImageName;

//                     // Get category ID
//                     $stmtCategoryId = $conn->prepare("SELECT category_id FROM Categories WHERE name = ?");
//                     $stmtCategoryId->bind_param("s", $category);
//                     $stmtCategoryId->execute();
//                     $resultCategoryId = $stmtCategoryId->get_result();

//                     if ($rowCategoryId = $resultCategoryId->fetch_assoc()) {
//                         $category_id = $rowCategoryId['category_id'];

//                         // Insert product details into database
//                         $stmtInsertProduct = $conn->prepare(
//                             "INSERT INTO Products (user_id, name, description, price, quantity, category_id, location, image_url) 
//                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
//                         );
//                         $stmtInsertProduct->bind_param(
//                             "issiiiss",
//                             $user_id,
//                             $name,
//                             $description,
//                             $price,
//                             $quantity,
//                             $category_id,
//                             $location,
//                             $databaseFilePath
//                         );

//                         if ($stmtInsertProduct->execute()) {
//                             echo json_encode(["success" => true, "message" => "Product added successfully!"]);
//                         } else {
//                             echo json_encode([
//                                 "success" => false, 
//                                 "message" => "Error adding product: " . htmlspecialchars($stmtInsertProduct->error)
//                             ]);
//                         }
//                         $stmtInsertProduct->close();
//                     } else {
//                         echo json_encode(["success" => false, "message" => "Invalid category selected."]);
//                     }
//                     $stmtCategoryId->close();
//                 } else {
//                     $error = error_get_last();
//                     echo json_encode([
//                         "success" => false, 
//                         "message" => "Error moving uploaded file. Details: " . ($error ? $error['message'] : 'Unknown error')
//                     ]);
//                 }
//             } else {
//                 echo json_encode([
//                     "success" => false, 
//                     "message" => "Invalid file type or size exceeded. Allowed types: JPEG, PNG, GIF. Max size: 2MB"
//                 ]);
//             }
//         } else {
//             $uploadError = $_FILES['imageUpload']['error'] ?? 'No file uploaded';
//             echo json_encode([
//                 "success" => false, 
//                 "message" => "Image upload failed. Error code: $uploadError"
//             ]);
//         }
//     } else {
//         echo json_encode(["success" => false, "message" => "All fields are required and must be valid."]);
//     }
// }

// $conn->close();
?>