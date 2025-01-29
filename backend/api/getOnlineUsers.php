<?php
// Connexion à la base de données
$host = '57.129.134.101'; // Votre hôte
$dbname = 'siteweb'; // Nom de la base de données
$username = 'root'; // Utilisateur de la base de données
$password = '12345678'; // Mot de passe de la base de données

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupérer tous les utilisateurs en ligne
function getOnlineUsers() {
    global $pdo;
    $sql = "SELECT id, firstname, photo FROM accounts WHERE status = 'online' or where status = 'offline'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Renvoyer les utilisateurs en ligne sous forme de JSON
header('Content-Type: application/json');
echo json_encode(getOnlineUsers());
?>
