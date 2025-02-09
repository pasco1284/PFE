<?php
session_start();
require 'vendor/autoload.php'; // Charger PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=localhost;dbname=siteweb", "root", "12345678");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        // Étape 1 : Envoi du code
        $email = $_POST['email'];

        // Vérifier si l'email existe dans la base de données
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
            $mail->Password = 'lzyk svyh hpsx hqbd'; // Mot de passe d'application
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('no-reply@ton-site.com', 'Support');
            $mail->addAddress($email);

            $mail->Subject = "Votre code de vérification";
            $mail->Body = "Votre code de vérification est : $verificationCode";

            // Vérifier si l'email est bien envoyé
            if ($mail->send()) {
                echo "Code envoyé avec succès, veuillez vérifier votre email.";
            } else {
                echo "Erreur d'envoi: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo "Erreur de PHPMailer: " . $mail->ErrorInfo;
        }

    } elseif (isset($_POST['verification_code'])) {
        // Étape 2 : Vérification du code
        if (!isset($_SESSION['verification_code'])) {
            echo "Aucun code de vérification trouvé.";
            exit();
        }

        if ($_SESSION['verification_code'] == $_POST['verification_code']) {
            $_SESSION['verified'] = true;
            echo "Code validé. Vous pouvez maintenant réinitialiser votre mot de passe.";
        } else {
            echo "Code incorrect.";
        }

    } elseif (isset($_POST['new_password'], $_POST['confirm_password'])) {
        // Étape 3 : Réinitialiser le mot de passe
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        $email = $_SESSION['user_email'];

        if ($newPassword === $confirmPassword) {
            // Hacher le mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe dans la base de données
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->execute(['password' => $hashedPassword, 'email' => $email]);

            // Suppression du code de vérification
            unset($_SESSION['verification_code']);
            unset($_SESSION['user_email']);

            echo "Mot de passe mis à jour avec succès.";
        } else {
            echo "Les mots de passe ne correspondent pas.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
</head>
<body>
    <div class="form-container">
        <!-- Étape 1 : Envoi du code -->
        <form method="POST" action="forget_password.php">
            <h2>Étape 1 : Envoyer le code</h2>
            <input type="email" name="email" placeholder="Entrez votre e-mail" required>
            <button type="submit">Envoyer le code</button>
        </form>

        <!-- Étape 2 : Vérification du code -->
        <form method="POST" action="forget_password.php">
            <h2>Étape 2 : Vérifier le code</h2>
            <input type="text" name="verification_code" placeholder="Entrez le code" required>
            <button type="submit">Vérifier le code</button>
        </form>

        <!-- Étape 3 : Réinitialisation du mot de passe -->
        <form method="POST" action="forget_password.php">
            <h2>Étape 3 : Nouveau mot de passe</h2>
            <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
            <button type="submit">Mettre à jour le mot de passe</button>
        </form>
    </div>
    <style>
    
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    height: 100vh;
    background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

#particles-js {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: -1;
}

.form-container {
    background: rgba(0, 0, 0, 0.267);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

h2 {
    text-align: center;
    color: #ffffff;
    margin-bottom: 20px;
}

.form-step {
    display: none;
    flex-direction: column;
}

.form-step.active {
    display: flex;
}

input[type="email"], input[type="text"], input[type="password"] {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
    font-size: 16px;
}

button {
    padding: 10px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button.next-btn {
    background: #021807;
    color: white;
}

button.prev-btn {
    background: #ffffff;
    color: black;
    margin-right: 10px;
}

button[type="submit"] {
    background: #007bff;
    color: white;
}

button:hover {
    opacity: 0.9;
}
    </style>
</body>
</html>
