<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit("Non autorisé");
}
$user_id = $_SESSION['user_id'];
$receiver_id = (int)($_POST['receiver_id'] ?? 0);

if ($receiver_id <= 0) {
    http_response_code(400);
    exit("ID conversation manquant");
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=siteweb", 'root', '12345678');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Soft delete
    $stmt = $pdo->prepare("UPDATE messages SET deleted_at = NOW() WHERE
        (sender_id = :me AND receiver_id = :them) OR (sender_id = :them AND receiver_id = :me)");
    $stmt->execute(['me' => $user_id, 'them' => $receiver_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
