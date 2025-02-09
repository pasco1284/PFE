<?php
// _Database.php : Fichier de connexion à la base de données avec mysqli

$servername = "localhost";
$username = "root"; // Utilisateur de la base de données
$password = "12345678"; // Mot de passe de la base de données
$dbname = "siteweb"; // Nom de la base de données

// Créer la connexion mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Optionnel : afficher "Connexion réussie" pour tester la connexion
    // echo "Connexion réussie";
}
?>
