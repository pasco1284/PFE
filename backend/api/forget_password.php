<?php
session_start();

include '_Database.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'louey.saadaoui10@gmail.com'; // Votre adresse Gmail
            $mail->Password = 'jjgm mihv otsa izdx'; // Mot de passe d'application
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
<div id="particles-js"></div>
    <script type="text/javascript" src="images/particles.js"></script>
    <script type="text/javascript" src="images/app-login.js"></script>
    
    <div class="form-container">
        <!-- Étape 1 : Envoi du code -->
        <?php if (!isset($_SESSION['verification_code'])): ?>
        <form method="POST" action="forget_password.php">
            <h2>Étape 1 : Envoyer le code</h2>
            <input type="email" name="email" placeholder="Entrez votre e-mail" required>
            <button type="submit">Envoyer le code</button>
        </form>
        <?php endif; ?>

        <!-- Étape 2 : Vérification du code -->
        <?php if (isset($_SESSION['verification_code']) && !isset($_SESSION['verified'])): ?>
        <form method="POST" action="forget_password.php">
            <h2>Étape 2 : Vérifier le code</h2>
            <input type="text" name="verification_code" placeholder="Entrez le code" required>
            <button type="submit">Vérifier le code</button>
        </form>
        <?php endif; ?>

        <!-- Étape 3 : Réinitialisation du mot de passe -->
        <?php if (isset($_SESSION['verified'])): ?>
        <form method="POST" action="forget_password.php">
            <h2>Étape 3 : Nouveau mot de passe</h2>
            <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
            <button type="submit">Mettre à jour le mot de passe</button>
        </form>
        <?php endif; ?>
    </div>
    <button class="button" onclick="window.history.back();">
  <svg class="svgIcon" viewBox="0 0 384 512">
    <path
      d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"
    ></path>
  </svg>
</button>
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

        .form-container {
            position: absolute;
            top: 40%;
            left: 40%;
            background: rgba(0, 0, 0, 0.267);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 120%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 20px;
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

        button[type="submit"] {
            background: #007bff;
            color: white;
        }

        button:hover {
            opacity: 0.9;
        }
        /* From Uiverse.io by vinodjangid07 */ 
.button {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: rgb(20, 20, 20);
  border: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0px 0px 0px 4px rgba(180, 160, 255, 0.253);
  cursor: pointer;
  transition-duration: 0.3s;
  overflow: hidden;
  position: absolute;
  top: 10%;
  right: 46%;
  rotate: 10px;
}

.svgIcon {
  width: 12px;
  transition-duration: 0.3s;
}

.svgIcon path {
  fill: white;
}

.button:hover {
  width: 140px;
  border-radius: 50px;
  transition-duration: 0.3s;
  background-color: rgb(181, 160, 255);
  align-items: center;
}

.button:hover .svgIcon {
  /* width: 20px; */
  transition-duration: 0.3s;
  transform: translateY(-200%);
}

.button::before {
  position: absolute;
  content: "Back";
  color: white;
  /* transition-duration: .3s; */
  font-size: 0px;
}

.button:hover::before {
  font-size: 13px;
  opacity: 1;
  bottom: unset;
  /* transform: translateY(-30px); */
  transition-duration: 0.3s;
}


    </style>
</body>
</html>
