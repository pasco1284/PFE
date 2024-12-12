<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    
    $to = "louey.saadaoui10@gmail.com"; // Remplacez par votre adresse email
    $headers = "From: $email" . "\r\n" .
               "Reply-To: $email" . "\r\n" .
               "X-Mailer: PHP/" . phpversion();
    
    $email_subject = "Contact Form Submission: $subject";
    $email_body = "Vous avez reçu un nouveau message de contact.\n\n".
                  "Nom: $name\n".
                  "Email: $email\n".
                  "Sujet: $subject\n".
                  "Message:\n$message\n";

    if (mail($to, $email_subject, $email_body, $headers)) {
        echo "Message envoyé avec succès!";
    } else {
        echo "Échec de l'envoi du message.";
    }
} else {
    echo "Méthode non autorisée.";
}
?>
