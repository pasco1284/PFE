<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $conn = new mysqli('localhost', 'root', '12345678', 'siteweb');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE accounts SET status = ?, last_activity = NOW() WHERE id = ?");
    $stmt->bind_param("si", $status, $user_id);
    $status = 'online';
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
