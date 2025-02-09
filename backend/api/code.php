<?php
session_start();
require 'vendor/autoload.php'; // Charger PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=siteweb", "root", "12345678");
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 0) {
        echo "Aucun compte associé à cet email.";
        exit();
    }

    // Générer un code de vérification
    $verificationCode = rand(100000, 999999);
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['user_email'] = $email;

    // Envoyer l'email avec PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'louey.saadaoui10@gmail.com'; // Ton adresse Gmail
        $mail->Password = 'lzyk svyh hpsx hqbd'; // Mot de passe d'application (PAS TON MOT DE PASSE NORMAL)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('no-reply@ton-site.com', 'Support');
        $mail->addAddress($email);

        $mail->Subject = "Votre code de vérification";
        $mail->Body = "Votre code de vérification est : $verificationCode";

        // Vérifier si l'email est bien envoyé
        if ($mail->send()) {
            // Redirection après envoi réussi
            header("Location: http://57.129.134.101/verification.php");
            exit;
        } else {
            echo "Erreur d'envoi: " . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        echo "Erreur de PHPMailer: " . $mail->ErrorInfo;
    }
}
?>
