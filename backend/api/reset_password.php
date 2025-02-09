<?php
session_start();

// Vérifier si l'utilisateur est bien passé par l'étape de vérification
if (!isset($_SESSION['user_email'])) {
    echo "Erreur : Aucun e-mail associé à la réinitialisation du mot de passe.";
    exit;
}

// Connexion à la base de données
$host = 'localhost';
$dbname = 'siteweb';
$username = 'root';
$password = '12345678';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_SESSION['user_email'];

    if ($newPassword === $confirmPassword) {
        // Hacher le mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Mettre à jour le mot de passe dans la base de données
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        $stmt->execute(['password' => $hashedPassword, 'email' => $email]);

        // Suppression du code de vérification
        unset($_SESSION['verification_code']);
        unset($_SESSION['user_email']);

        echo "Mot de passe mis à jour avec succès.";
    } else {
        echo "Les mots de passe ne correspondent pas.";
    }
}
?>