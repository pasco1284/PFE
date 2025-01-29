<?php
// Connexion à la base de données
$host = '57.129.134.101';
$dbname = 'siteweb'; // Nom de votre base de données
$username = 'root'; // Votre nom d'utilisateur MySQL
$password = '12345678'; // Votre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Échec de la connexion : " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_SESSION['user_email'];

    if ($newPassword === $confirmPassword) {
        // Hash du mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Mise à jour dans la base de données
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            echo "Mot de passe mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour du mot de passe.";
        }
    } else {
        echo "Les mots de passe ne correspondent pas.";
    }
}
?>
