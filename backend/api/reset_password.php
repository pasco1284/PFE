<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$host = "localhost";
$dbname = "siteweb";
$username = "root";
$password = "12345678";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$step = 1;
$errorMessage = '';
$successMessage = '';
$email = $_POST['email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['step'] == 1) {
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $code = rand(100000, 999999);
            $stmt = $pdo->prepare("UPDATE accounts SET reset_code = ? WHERE email = ?");
            $stmt->execute([$code, $email]);

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'louey.saadaoui10@gmail.com';
                $mail->Password = 'jjgm mihv otsa izdx';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('louey.saadaoui10@gmail.com', 'Support');
                $mail->addAddress($email);
                $mail->Subject = 'Code de réinitialisation';
                $mail->Body = "Votre code de réinitialisation est : $code";
                $mail->send();

                $step = 2;
                $successMessage = "Le code a été envoyé à votre adresse e-mail.";
            } catch (Exception $e) {
                $errorMessage = "Erreur d'envoi de l'e-mail : " . $mail->ErrorInfo;
            }
        } else {
            $errorMessage = "Aucun compte trouvé avec cet e-mail.";
        }
    }

    if ($_POST['step'] == 2) {
        $code = $_POST['code'];

        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ? AND reset_code = ?");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            $step = 3;
        } else {
            $errorMessage = "Code invalide. Veuillez réessayer.";
            $step = 2;
        }
    }

    if ($_POST['step'] == 3) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $errorMessage = "Les mots de passe ne correspondent pas.";
            $step = 3;
        } else {
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE accounts SET password = ?, reset_code = NULL WHERE email = ?");
            if ($stmt->execute([$hashed, $email])) {
                header("Location: http://57.129.134.101/login");
                exit();
            } else {
                $errorMessage = "Erreur lors de la mise à jour du mot de passe.";
                $step = 3;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #particles-js {
        height: 100%;
        }
  
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            position: absolute;
            top: 40%;
            left: 50;
            padding: 25px;
            border-radius: 10px;
            color: white;
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .error {
            color: #ff6666;
            text-align: center;
        }
        .success {
            color: #66ff99;
            text-align: center;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
    </style>
</head>
<body>

<div id="particles-js"></div>
    <script type="text/javascript" src="images/particles.js"></script>
    <script type="text/javascript" src="images/app-login.js"></script>

<div class="form-container">
    <?php if ($errorMessage): ?><div class="error"><?= htmlspecialchars($errorMessage) ?></div><?php endif; ?>
    <?php if ($successMessage): ?><div class="success"><?= htmlspecialchars($successMessage) ?></div><?php endif; ?>

    <!-- Étape 1 -->
    <form method="POST" class="form-step <?= $step == 1 ? 'active' : '' ?>">
        <h2>Étape 1 : Entrez votre e-mail</h2>
        <input type="email" name="email" placeholder="Email" required>
        <input type="hidden" name="step" value="1">
        <button type="submit">Envoyer le code</button>
    </form>

    <!-- Étape 2 -->
    <form method="POST" class="form-step <?= $step == 2 ? 'active' : '' ?>">
        <h2>Étape 2 : Vérification du code</h2>
        <input type="text" name="code" placeholder="Code reçu par email" required>
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="step" value="2">
        <button type="submit">Vérifier</button>
    </form>

    <!-- Étape 3 -->
    <form method="POST" class="form-step <?= $step == 3 ? 'active' : '' ?>">
        <h2>Étape 3 : Nouveau mot de passe</h2>
        <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
        <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="step" value="3">
        <button type="submit">Réinitialiser</button>
    </form>
</div>
</body>
</html>
