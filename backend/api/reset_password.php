<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: forget_password.html");
    exit();
}

$host = "localhost";
$dbname = "siteweb";
$username = "root";
$password = "12345678";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_SESSION['reset_email'];

    // Mettre à jour le mot de passe et supprimer le code
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_code = NULL WHERE email = ?");
    $stmt->execute([$new_password, $email]);

    // Nettoyer la session et rediriger vers la connexion
    session_destroy();
    echo "Mot de passe réinitialisé avec succès. <a href='login.html'>Connectez-vous</a>";
}
?>
