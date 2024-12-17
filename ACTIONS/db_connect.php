<?php
// Database connection parameters
$host = "localhost"; // Database host
$username = "nonzuzo.sikhosana"; // Database username
$password = "Mcebo1014"; // Database password (default for XAMPP)
$dbname = "webtech_fall2024_nonzuzo_sikhosana"; // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
return $conn;

?>