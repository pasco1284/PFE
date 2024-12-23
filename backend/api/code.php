<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    // Générer un code de vérification
    $verificationCode = rand(100000, 999999);
    $_SESSION['verification_code'] = $verificationCode;
    $_SESSION['user_email'] = $email;

    // Envoyer un e-mail avec le code
    $subject = "Votre code de vérification";
    $message = "Bonjour,\nVoici votre code de vérification : $verificationCode";
    $headers = "From: no-reply@votre-site.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "Code envoyé avec succès à $email";
    } else {
        echo "Erreur lors de l'envoi du code.";
    }
}
?>
