<?php


// Vérifier si le formulaire a été soumis
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

    // Vérifier si l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse e-mail invalide.";
        exit;
    }

    // Configuration de l'e-mail
    $to = "louey.saadaoui10@gmail.com"; 
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Contenu de l'e-mail
    $emailContent = "Nom: $name\n";
    $emailContent .= "E-mail: $email\n";
    $emailContent .= "Sujet: $subject\n\n";
    $emailContent .= "Message:\n$message\n";

    // Envoi de l'e-mail
    if (mail($to, $subject, $emailContent, $headers)) {
        echo "Votre message a été envoyé avec succès.";
    } else {
        echo "Erreur lors de l'envoi de l'e-mail. Veuillez réessayer plus tard.";
    }
} else {
    // Rediriger si la méthode de requête n'est pas POST
    header("Location: /");
    exit;
}
?>
