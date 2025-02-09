<?php
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
    $email = $_POST['email'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Mettre à jour le mot de passe
    $stmt = $pdo->prepare("UPDATE accounts SET password = ?, reset_code = NULL WHERE email = ?");
    if ($stmt->execute([$new_password, $email])) {
        echo "Mot de passe réinitialisé avec succès.";
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>
