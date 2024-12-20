<?php
session_start();
include '_Database.php'; // Inclure le fichier de connexion à la base de données

// Vérification des données envoyées par le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    if ($name && $email && $subject && $message) {
        try {
            // Enregistrement dans la base de données
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':subject' => $subject,
                ':message' => $message
            ]);

            // Envoi d'un e-mail de confirmation
            $to = $email;
            $headers = "From: no-reply@yoursite.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $email_subject = "Confirmation de réception de votre message";
            $email_body = "Bonjour $name,\n\nMerci de nous avoir contactés. Voici un récapitulatif de votre message :\n\nSujet : $subject\n\nMessage :\n$message\n\nNous vous répondrons dans les plus brefs délais.\n\nCordialement,\nL'équipe Support.";

            mail($to, $email_subject, $email_body, $headers);

            // Redirection avec un message de succès
            header("Location: http://localhost:4321/contact?success=1");
            exit;
        } catch (Exception $e) {
            die("Erreur : " . $e->getMessage());
        }
    } else {
        header("Location: http://localhost:4321/contact?error=1");
        exit;
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Méthode non autorisée.";
    exit;
}
?>
