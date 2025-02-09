<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];

$conn = new mysqli('localhost', 'root', '12345678', 'siteweb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) VALUES (?, ?, ?, 'text')");
$stmt->bind_param("iis", $user_id, $receiver_id, $message);
$stmt->execute();
$stmt->close();

echo json_encode(['status' => 'success']);
$conn->close();
?>
