<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'siteweb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requêtes pour récupérer les logs
    $accountLogs = $pdo->query("SELECT * FROM account_logs")->fetchAll(PDO::FETCH_ASSOC);
    $trafficLogs = $pdo->query("SELECT * FROM traffic_logs")->fetchAll(PDO::FETCH_ASSOC);
    $fileLogs = $pdo->query("SELECT * FROM file_logs")->fetchAll(PDO::FETCH_ASSOC);
    $conversationLogs = $pdo->query("SELECT * FROM conversation_logs")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'accountLogs' => $accountLogs,
        'trafficLogs' => $trafficLogs,
        'fileLogs' => $fileLogs,
        'conversationLogs' => $conversationLogs
    ]);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}
?>