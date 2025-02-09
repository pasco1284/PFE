<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "siteweb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $code = $_POST['code'];

    $stmt = $conn->prepare("SELECT * FROM password_reset WHERE email = ? AND code = ? 
                            AND created_at >= NOW() - INTERVAL 10 MINUTE");
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Code valide. Veuillez définir un nouveau mot de passe.";
        echo "<form action='http://57.129.134.101/reset_password.php' method='POST'>
                  <input type='hidden' name='email' value='$email'>
                  <label for='password'>Nouveau mot de passe :</label>
                  <input type='password' name='password' required>
                  <button type='submit'>Réinitialiser</button>
              </form>";
    } else {
        echo "Code invalide ou expiré.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<body>
<form action="http://57.129.134.101/verify_code.php" method="POST">
    <label for="code">Entrez le code reçu :</label>
    <input type="text" name="code" required>
    <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>">
    <button type="submit">Vérifier</button>
</form>
</body>
</html>

