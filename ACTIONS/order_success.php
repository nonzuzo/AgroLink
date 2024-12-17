<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            color: #28a745;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }

        .button-container {
            display: flex; /* Use flexbox to align buttons */
            justify-content: center; /* Center the buttons */
            gap: 10px; /* Space between buttons */
        }

        a {
            text-decoration: none;
            font-size: 16px;
            color: white;
            background-color: #28a745; /* Continue Shopping button */
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #218838; /* Darker shade on hover */
        }

        .logout-button {
            background-color: #dc3545; /* Logout button color */
        }

        .logout-button:hover {
            background-color: #c82333; /* Darker shade on hover */
        }

        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>Thank You for Your Order!</h1>
        <p>Your order has been successfully placed.</p>
        
        <!-- Optionally display order details -->
        
        <div class="button-container">
            <a href="product_listing.php">Continue Shopping</a>
            
            <!-- Logout Button -->
            <a href="logout.php" class="logout-button">Logout</a>
        </div>

        <div class="footer">
            <p>&copy; 2024 AgroLink. All rights reserved.</p>
        </div>
    </div>

</body>
</html>