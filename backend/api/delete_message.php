<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit("Non autorisé");
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = (int)($_POST['message_id'] ?? 0);
    if ($message_id <= 0) {
        http_response_code(400);
        exit("Message ID manquant");
    }

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=siteweb", 'root', '12345678');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Option 1: Marquer comme supprimé (soft delete) si l'utilisateur est l'expéditeur ou destinataire
        $stmt = $pdo->prepare("UPDATE messages SET deleted_at = NOW() WHERE id = :id AND (sender_id = :user OR receiver_id = :user)");
        $stmt->execute(['id' => $message_id, 'user' => $user_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    exit("Méthode non autorisée");
}
