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

///////////////////////////////added 

// Handle suspension action
if (isset($_GET['action']) && $_GET['action'] === 'suspend' && isset($_GET['user_id'])) {
    $user_id = $conn->real_escape_string($_GET['user_id']);
    
    // Prepare and execute suspension query
    $suspendQuery = "UPDATE Users SET status = 'suspended' WHERE user_id = '$user_id'";
    
    if ($conn->query($suspendQuery)) {
        // Redirect to prevent form resubmission and refresh the page
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Handle query error
        $error_message = "Error suspending user: " . $conn->error;
    }
}

// Set default pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch Users
$userQuery = "SELECT user_id, name, email, status, user_type FROM Users WHERE status IN ('registered', 'approved') LIMIT $limit OFFSET $offset";
$userResult = $conn->query($userQuery);
$users = $userResult ? $userResult->fetch_all(MYSQLI_ASSOC) : [];

// Count the total registered buyers and approved farmers for pagination
$totalUsersQuery = "SELECT COUNT(*) as total FROM Users WHERE status IN ('registered', 'approved')";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult ? $totalUsersResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalUsers / $limit);

// Fetch counts for user and status graphs
$userCounts = [
    'registeredBuyers' => 0,
    'registeredFarmers' => 0,
    'approvedFarmers' => 0,
    'suspendedBuyers' => 0,
    'suspendedFarmers' => 0,
];

// Query to get user counts
$userCountQuery = "SELECT user_type, status FROM Users";
$userCountResult = $conn->query($userCountQuery);
if ($userCountResult) {
    while ($row = $userCountResult->fetch_assoc()) {
        if ($row['status'] === 'registered' && $row['user_type'] === 'buyer') {
            $userCounts['registeredBuyers']++;
        } elseif ($row['status'] === 'registered' && $row['user_type'] === 'farmer') {
            $userCounts['registeredFarmers']++;
        } elseif ($row['status'] === 'approved' && $row['user_type'] === 'farmer') {
            $userCounts['approvedFarmers']++;
        } elseif ($row['status'] === 'suspended' && $row['user_type'] === 'buyer') {
            $userCounts['suspendedBuyers']++;
        } elseif ($row['status'] === 'suspended' && $row['user_type'] === 'farmer') {
            $userCounts['suspendedFarmers']++;
        }
    }
}

// Count product statuses (same as before)
$approvedCount = 0;
$pendingCount = 0;
$rejectedCount = 0;

$productQuery = "SELECT status FROM Products";
$productResult = $conn->query($productQuery);
if ($productResult) {
    while ($product = $productResult->fetch_assoc()) {
        if ($product['status'] === 'approved') {
            $approvedCount++;
        } elseif ($product['status'] === 'pending') {
            $pendingCount++;
        } elseif ($product['status'] === 'rejected') {
            $rejectedCount++;
        }
    }
}

// Count orders by status (same as before)
$completedOrders = 0;
$cancelledOrders = 0;
$pendingOrders = 0;

$orderQuery = "SELECT status FROM Orders";
$orderResult = $conn->query($orderQuery);
if ($orderResult) {
    while ($order = $orderResult->fetch_assoc()) {
        if ($order['status'] === 'Completed') {
            $completedOrders++;
        } elseif ($order['status'] === 'Cancelled') {
            $cancelledOrders++;
        } else {
            $pendingOrders++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../CSS/admin_dashboard.css"> <!-- Add CSS for styling -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js CDN -->
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
            <h1>Welcome, Admin <?php echo htmlspecialchars($_SESSION['name']); ?></h1>

            <!-- Dashboard Overview Section -->
            <section class="dashboard-overview">
                <div class="graphs">
                    <div class="graph-container">
                        <canvas id="userStatusChart"></canvas>
                    </div>
                    <div class="graph-container">
                        <canvas id="productStatusChart"></canvas>
                    </div>
                    <div class="graph-container">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </section>

            <hr>

            <!-- Users Section -->
            <section>
                <h2>Users</h2>
                <table class="data-table">
                    <tr>
                         
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                    <tr>
                         
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($user['user_type'])); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($user['status'])); ?></td>
                        <td>
                            <?php if ($user['status'] === 'approved' || $user['status'] === 'registered'): ?>
                            <a href="admin_dashboard.php?action=suspend&user_id=<?php echo urlencode($user['user_id']); ?>">Suspend</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Pagination Controls -->
                <div class="pagination">
                    <button <?php echo $page <= 1 ? 'disabled' : ''; ?> class="prev" onclick="window.location.href='admin_dashboard.php?page=<?php echo $page - 1; ?>'">Previous</button>
                    <button <?php echo $page >= $totalPages ? 'disabled' : ''; ?> class="next" onclick="window.location.href='admin_dashboard.php?page=<?php echo $page + 1; ?>'">Next</button>
                </div>
            </section>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script type="text/javascript">
        const ctx1 = document.getElementById('userStatusChart').getContext('2d');
        const ctx2 = document.getElementById('productStatusChart').getContext('2d');
        const ctx3 = document.getElementById('orderStatusChart').getContext('2d');

        // User Status Chart
        const userStatusChart = new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['Registered Buyers', 'Registered Farmers', 'Approved Farmers', 'Suspended Buyers', 'Suspended Farmers'],
                datasets: [{
                    label: 'User Status',
                    data: [<?php echo $userCounts['registeredBuyers']; ?>, <?php echo $userCounts['registeredFarmers']; ?>, <?php echo $userCounts['approvedFarmers']; ?>, <?php echo $userCounts['suspendedBuyers']; ?>, <?php echo $userCounts['suspendedFarmers']; ?>],
                    backgroundColor: ['#FF7043', '#388E3C', '#4CAF50', '#D32F2F', '#81C784'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });

        // Product Status Chart
        const productStatusChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    label: 'Product Status',
                    data: [<?php echo $approvedCount; ?>, <?php echo $pendingCount; ?>, <?php echo $rejectedCount; ?>],
                    backgroundColor: ['#388E3C', '#81C784', '#D32F2F'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });

        // Order Status Chart
        const orderStatusChart = new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Cancelled', 'Pending'],
                datasets: [{
                    label: 'Order Status',
                    data: [<?php echo $completedOrders; ?>, <?php echo $cancelledOrders; ?>, <?php echo $pendingOrders; ?>],
                    backgroundColor: ['#4CAF50', '#D32F2F', '#FF7043'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
    </script>
</body>
</html>
