<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$audio = $_FILES['audio'];

$target_dir = "../audio/";
$target_file = $target_dir . basename($audio["name"]);

if (move_uploaded_file($audio["tmp_name"], $target_file)) {
    $conn = new mysqli('localhost', 'root', '12345678', 'siteweb');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) VALUES (?, ?, ?, 'audio')");
    $audio_url = '/audio/' . basename($audio["name"]);
    $stmt->bind_param("iis", $user_id, $receiver_id, $audio_url);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success', 'audio_url' => $audio_url]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
}
?>
