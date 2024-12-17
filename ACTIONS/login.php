<?php
session_start();

// Include the database connection file
include 'db_connect.php';

global $conn;

// Enable error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the Content-Type to JSON
header('Content-Type: application/json');

// Initialize response array
$res = [];

// Read JSON input
$json = file_get_contents("php://input");
$data = json_decode($json);

if (!$data) {
    $res['status'] = 'error';
    $res['message'] = 'Invalid input.';
    echo json_encode($res);
    exit();
}

// Collect and trim input values
$email = trim($data->email);
$password = trim($data->password);
$userTypeInput = trim($data->userType);

// Input validation
$emailRegex = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
$passwordRegex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/";

if (empty($email) || !preg_match($emailRegex, $email)) {
    $res['status'] = 'error';
    $res['message'] = 'Invalid or missing email.';
    echo json_encode($res);
    exit();
}

if (empty($password) || !preg_match($passwordRegex, $password)) {
    $res['status'] = 'error';
    $res['message'] = 'Invalid or missing password.';
    echo json_encode($res);
    exit();
}

if (empty($userTypeInput)) {
    $res['status'] = 'error';
    $res['message'] = 'User type is required.';
    echo json_encode($res);
    exit();
}

// Prepare SQL statement
$stmt = $conn->prepare("SELECT user_id, password, name, user_type, status FROM Users WHERE email=?");
if (!$stmt) {
    $res['status'] = 'error';
    $res['message'] = 'Database error: ' . $conn->error;
    echo json_encode($res);
    exit();
}

// Bind and execute
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $res['status'] = 'error';
    $res['message'] = 'No account found with that email.';
    echo json_encode($res);
    exit();
}

$row = $result->fetch_assoc();
$hashedPassword = $row['password'];
$userTypeDB = $row['user_type'];
$statusDB = $row['status'];

// // Validate password
// if (!password_verify($password, $hashedPassword)) {
//     $res['status'] = 'error';
//     $res['message'] = 'Invalid password.';
//     echo json_encode($res);
//     exit();
// }

// Validate user type and status
if (
    (strtolower($userTypeInput) === 'farmer' && $statusDB !== 'approved') ||
    (in_array(strtolower($userTypeInput), ['buyer', 'admin']) && $statusDB !== 'registered')
) {
    $res['status'] = 'error';
    $res['message'] = "Login not permitted for this user type and status.";
    echo json_encode($res);
    exit();
}

if (strtolower($userTypeInput) !== $userTypeDB) {
    $res['status'] = 'error';
    $res['message'] = "You cannot log in as a " . $userTypeInput . ". This is a " . $userTypeDB . " account.";
    echo json_encode($res);
    exit();
}

// Set session variables
$_SESSION['email'] = $email;
$_SESSION['name'] = $row['name'];
$_SESSION['user_type'] = $userTypeDB;
$_SESSION['user_id'] = $row['user_id'];

// Return success response
$res['status'] = 'success';
$res['userType'] = $userTypeDB;
echo json_encode($res);
exit();
?>
