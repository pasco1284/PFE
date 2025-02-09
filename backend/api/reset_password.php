<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Inclure PHPMailer

$host = "localhost"; // Change si nécessaire
$dbname = "siteweb"; // Remplace par le nom de ta base de données
$username = "root"; // Remplace par ton utilisateur MySQL
$password = "12345678"; // Ton mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $step = $_POST['step'] ?? 1; // On détermine quelle étape est en cours

    // Étape 1 : Envoi du code
    if ($step == 1) {
        $email = $_POST['email'];

        // Vérifier si l'email existe dans la table accounts
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $code = rand(100000, 999999); // Générer un code à 6 chiffres

            // Enregistrer le code dans la base de données
            $stmt = $pdo->prepare("UPDATE accounts SET reset_code = ? WHERE email = ?");
            $stmt->execute([$code, $email]);

            // Configurer PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'louey.saadaoui10@gmail.com'; // Ton email
                $mail->Password = 'jjgm mihv otsa izdx'; // Mot de passe d’application Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('louey.saadaoui10@gmail.com', 'Support');
                $mail->addAddress($email);
                $mail->Subject = 'Code de réinitialisation de mot de passe';
                $mail->Body = "Votre code de réinitialisation est : $code";

                $mail->send();
                $step = 2; // Passer à l'étape 2
            } catch (Exception $e) {
                echo "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
            }
        } else {
            echo "Aucun compte associé à cet email.";
        }
    }

    // Étape 2 : Vérification du code
    if ($step == 2) {
        $email = $_POST['email'];
        $code = $_POST['code'];

        // Vérifier si le code est correct
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ? AND reset_code = ?");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            $step = 3; // Passer à l'étape 3
        } else {
            echo "Code incorrect.";
        }
    }

    // Étape 3 : Réinitialiser le mot de passe
    if ($step == 3) {
        $email = $_POST['email'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

        // Mettre à jour le mot de passe
        $stmt = $pdo->prepare("UPDATE accounts SET password = ?, reset_code = NULL WHERE email = ?");
        if ($stmt->execute([$new_password, $email])) {
            echo "Mot de passe réinitialisé avec succès.";
        } else {
            echo "Erreur lors de la mise à jour.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/icon.png">
    <title>Forget Password</title>
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
</head>
<body>
    <div class="form-container">
        <!-- Étape 1 : Envoi du code -->
        <form method="POST" action="" class="form-step <?php echo $step == 1 ? 'active' : ''; ?>">
            <h2>Étape 1 : Envoyer le code</h2>
            <input type="email" name="email" placeholder="Entrez votre e-mail" required>
            <input type="hidden" name="step" value="1">
            <button type="submit" class="next-btn">Suivant</button>
        </form>

        <!-- Étape 2 : Vérification du code -->
        <form method="POST" action="" class="form-step <?php echo $step == 2 ? 'active' : ''; ?>">
            <h2>Étape 2 : Vérifier le code</h2>
            <input type="text" name="code" placeholder="Entrez le code" required>
            <input type="hidden" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
            <input type="hidden" name="step" value="2">
            <button type="submit" class="next-btn">Suivant</button>
        </form>

        <!-- Étape 3 : Réinitialiser le mot de passe -->
        <form method="POST" action="" class="form-step <?php echo $step == 3 ? 'active' : ''; ?>">
            <h2>Étape 3 : Nouveau mot de passe</h2>
            <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
            <input type="hidden" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
            <input type="hidden" name="step" value="3">
            <button type="submit">Mettre à jour</button>
        </form>
    </div>
</body>
</html>
