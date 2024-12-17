<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $input=json_decode(file_get_contents("php://input"), true);
    if(!isset($input['cart_item_id'])){
        header("HTTP/1.1 Bad Request");
        json_encode(['success'=>false ,'error'=> 'cart_item_id is missing']);
        exit();
    }


    $cart_item_id = intval($input['cart_item_id']);

    $stmt = $conn->prepare("DELETE FROM Cart_Items WHERE cart_item_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_item_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete item']);
    }

    $stmt->close();
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();
?>
