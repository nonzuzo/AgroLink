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

// Fetch farmers awaiting approval (status = 'registered')
$farmerQuery = "SELECT user_id, name, email, status FROM Users WHERE user_type='farmer' AND status = 'registered'";
$farmerResult = $conn->query($farmerQuery);
$farmers = $farmerResult->fetch_all(MYSQLI_ASSOC);

// Approve or Reject Farmer
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $user_id = $_GET['user_id']; // use user_id instead of farmer_id

    if ($action === 'approve') {
        // Change status to 'approved'
        $updateQuery = "UPDATE Users SET status = 'approved' WHERE user_id = ?";
        $_SESSION['status_message'] = 'Farmer has been successfully approved.';
    } elseif ($action === 'reject') {
        // Change status to 'rejected' or 'suspended' (based on your preference)
        $updateQuery = "UPDATE Users SET status = 'suspended' WHERE user_id = ?";
        $_SESSION['status_message'] = 'Farmer has been successfully rejected.';
    }

    if ($stmt = $conn->prepare($updateQuery)) {
        $stmt->bind_param("i", $user_id); // use user_id
        $stmt->execute();
        header("Location: farmer_approvals.php");
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
    <title>Farmer Approvals</title>
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
    <style>
        /* CSS for displaying the status message */
        .status-message {
            color: green; /* Success message color */
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
            <h1>Farmer Approvals</h1>

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
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($farmers as $farmer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($farmer['name']); ?></td>
                        <td><?php echo htmlspecialchars($farmer['email']); ?></td>
                        <td><?php echo htmlspecialchars($farmer['status']); ?></td>
                        <td>
                            <a href="farmer_approvals.php?action=approve&user_id=<?php echo urlencode($farmer['user_id']); ?>">Approve</a> | 
                            <a href="farmer_approvals.php?action=reject&user_id=<?php echo urlencode($farmer['user_id']); ?>">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
