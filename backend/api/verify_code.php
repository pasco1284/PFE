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
    $code = $_POST['code'];

    // Vérifier si le code est correct
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ? AND reset_code = ?");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Code valide. Vous pouvez changer votre mot de passe.";
    } else {
        echo "Code incorrect.";
    }
}
?>
