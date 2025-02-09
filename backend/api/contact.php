<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Si tu utilises Composer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validation des champs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    // Vérification de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse e-mail invalide.";
        exit;
    }

    // Créer une nouvelle instance de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Paramètres du serveur SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Exemple pour Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'louey.saadaoui10@gmail.com'; // Remplace par ton adresse Gmail
        $mail->Password = 'jjgm mihv otsa izdx'; // Utilise un mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataire et expéditeur
        $mail->setFrom('louey.saadaoui10@gmail.com', 'Nom'); // L'email de ton compte Gmail
        $mail->addAddress('louey.saadaoui10@gmail.com'); // Adresse où l'e-mail sera envoyé
        $mail->Subject = $subject;
        $mail->Body = "Nom: $name\nEmail: $email\n\nMessage:\n$message";

        // Envoi de l'e-mail
        $mail->send();
        echo "Votre message a été envoyé avec succès.";
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'e-mail: {$mail->ErrorInfo}";
    }
}
?>