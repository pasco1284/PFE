<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "siteweb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        echo "Mot de passe réinitialisé avec succès.";
    } else {
        echo "Erreur.";
    }
}
?>
