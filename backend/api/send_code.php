<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Inclure PHPMailer

$host = "localhost"; // Change si nécessaire
$dbname = "siteweb"; // Remplace par le nom de ta base de données
$username = "root"; // Remplace par ton utilisateur MySQL
$password = "12345678"; // Ton mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Vérifier si l'email existe dans la table accounts
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $code = rand(100000, 999999); // Générer un code à 6 chiffres

        // Enregistrer le code dans la base de données
        $stmt = $pdo->prepare("UPDATE accounts SET reset_code = ? WHERE email = ?");
        $stmt->execute([$code, $email]);

        // Configurer PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'louey.saadaoui10@gmail.com'; // Ton email
            $mail->Password = 'jjgm mihv otsa izdx'; // Mot de passe d’application Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('louey.saadaoui10@gmail.com', 'Support');
            $mail->addAddress($email);
            $mail->Subject = 'Code de réinitialisation de mot de passe';
            $mail->Body = "Votre code de réinitialisation est : $code";

            $mail->send();
            
            // Redirection vers l'étape 2 après l'envoi réussi
            echo "<script type='text/javascript'>
                    window.location.href = 'http://57.129.134.101/verification_code'; // Change l'URL selon ton besoin
                    alert('Un email avec le code de réinitialisation a été envoyé.');
                </script>";
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
        }
    } else {
        echo "Aucun compte associé à cet email.";
    }
}
?>
