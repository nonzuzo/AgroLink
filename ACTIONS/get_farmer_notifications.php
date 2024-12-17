<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['user_id']; // Assuming you store user_id in session

$stmt = $conn->prepare("SELECT message FROM Notifications WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);

$stmt->close();
$conn->close();
?>