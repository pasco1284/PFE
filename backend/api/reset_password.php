<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Charger PHPMailer

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

$step = $_POST['step'] ?? 1;
$errorMessage = '';
$successMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($step == 1) {
        $email = $_POST['email'];

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
                $mail->Username = 'louey.saadaoui10@gmail.com'; // Remplace par ton email
                $mail->Password = 'jjgm mihv otsa izdx'; // Remplace par ton mot de passe d'application Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('louey.saadaoui10@gmail.com', 'Support');
                $mail->addAddress($email);
                $mail->Subject = 'Code de réinitialisation';
                $mail->Body = "Voici votre code de réinitialisation : $code";

                $mail->send();
                $step = 2;
                $successMessage = "Code envoyé à votre adresse email.";
            } catch (Exception $e) {
                $errorMessage = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
            }
        } else {
            $errorMessage = "Aucun compte associé à cet email.";
        }
    }

    if ($step == 2) {
        $email = $_POST['email'];
        $code = $_POST['code'];

        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ? AND reset_code = ?");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            $step = 3;
        } else {
            $errorMessage = "Code invalide. Réessayez.";
            $step = 2;
        }
    }

    if ($step == 3) {
        $email = $_POST['email'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $errorMessage = "Les mots de passe ne correspondent pas.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE accounts SET password = ?, reset_code = NULL WHERE email = ?");
            if ($stmt->execute([$hashed, $email])) {
                header("Location: http://57.129.134.101/login");
                exit();
            } else {
                $errorMessage = "Erreur lors de la mise à jour.";
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
            height: 100vh;
            background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            position: relative;
            transform: translateY(-10%);
            color: white;
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
            margin-bottom: 10px;
        }

        .success {
            color: #66ff99;
            text-align: center;
            margin-bottom: 10px;
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
    <div class="form-container">
        <?php if (!empty($errorMessage)): ?>
            <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <!-- Étape 1 : Email -->
        <form method="POST" class="form-step <?= $step == 1 ? 'active' : '' ?>">
            <h2>Étape 1 : Entrez votre e-mail</h2>
            <input type="email" name="email" placeholder="Email" required>
            <input type="hidden" name="step" value="1">
            <button type="submit">Envoyer le code</button>
        </form>

        <!-- Étape 2 : Code -->
        <form method="POST" class="form-step <?= $step == 2 ? 'active' : '' ?>">
            <h2>Étape 2 : Entrez le code</h2>
            <input type="text" name="code" placeholder="Code à 6 chiffres" required>
            <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <input type="hidden" name="step" value="2">
            <button type="submit">Vérifier</button>
        </form>

        <!-- Étape 3 : Nouveau mot de passe -->
        <form method="POST" class="form-step <?= $step == 3 ? 'active' : '' ?>">
            <h2>Étape 3 : Nouveau mot de passe</h2>
            <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
            <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <input type="hidden" name="step" value="3">
            <button type="submit">Réinitialiser</button>
        </form>
    </div>
</body>
</html>
