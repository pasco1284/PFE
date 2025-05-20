<?php
session_start();
$host = "localhost";
$dbname = "siteweb";
$username = "root";
$password = "12345678";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
} catch (PDOException $e) {
    die(json_encode(["error" => $e->getMessage()]));
}

$currentUserId = $_SESSION['user_id'] ?? 1;
$receiver_id = $_GET['receiver_id'] ?? null;

if ($receiver_id) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE 
        (sender_id = ? AND receiver_id = ?) OR 
        (sender_id = ? AND receiver_id = ?) 
        ORDER BY id ASC");
    $stmt->execute([$currentUserId, $receiver_id, $receiver_id, $currentUserId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);
} else {
    echo json_encode([]);
}
