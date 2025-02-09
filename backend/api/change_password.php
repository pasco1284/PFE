<?php
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

session_start();

// Étape 1: Envoi du code de réinitialisation
if (isset($_POST['send_code'])) {
    $email = $_POST['email'];

    // Vérifier si l'email existe
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Générer un code de réinitialisation unique
        $reset_code = rand(100000, 999999);

        // Mettre à jour le code dans la base de données
        $update_stmt = $pdo->prepare("UPDATE accounts SET reset_code = ? WHERE email = ?");
        $update_stmt->execute([$reset_code, $email]);

        // Envoyer le code par email (ici juste une simulation)
        $_SESSION['email'] = $email;
        $_SESSION['reset_code'] = $reset_code; // Stocker temporairement le code pour la validation

        // Passer à l'étape de vérification
        $step = 2;
    } else {
        $error_message = "Aucun utilisateur trouvé avec cet email.";
    }
}

// Étape 2: Vérification du code
if (isset($_POST['verify_code'])) {
    $email = $_SESSION['email'];
    $code = $_POST['code'];

    // Vérifier si le code est correct
    if ($_SESSION['reset_code'] == $code) {
        // Passer à l'étape de changement de mot de passe
        $step = 3;
    } else {
        $error_message = "Code incorrect.";
    }
}

// Étape 3: Changement du mot de passe
if (isset($_POST['change_password'])) {
    $email = $_SESSION['email'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe dans la base de données
    $update_stmt = $pdo->prepare("UPDATE accounts SET password = ? WHERE email = ?");
    $update_stmt->execute([$new_password, $email]);

    $success_message = "Votre mot de passe a été changé avec succès!";
    // Réinitialiser la session
    session_unset();
    session_destroy();
    $step = 1; // Retour à l'étape 1 pour une nouvelle tentative
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le Mot de Passe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error,
        .success {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (isset($error_message)) { ?>
        <p class="error"><?= $error_message; ?></p>
    <?php } ?>
    <?php if (isset($success_message)) { ?>
        <p class="success"><?= $success_message; ?></p>
    <?php } ?>

    <!-- Étape 1: Envoi du code -->
    <div class="step <?= ($step == 1) ? 'active' : ''; ?>">
        <h2>Réinitialiser votre mot de passe</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Entrez votre email :</label>
                <input type="email" id="email" name="email" required placeholder="Votre email">
            </div>
            <button type="submit" name="send_code">Envoyer le code</button>
        </form>
    </div>

    <!-- Étape 2: Vérification du code -->
    <div class="step <?= ($step == 2) ? 'active' : ''; ?>">
        <h2>Vérification du code</h2>
        <form method="POST">
            <div class="form-group">
                <label for="code">Entrez le code reçu :</label>
                <input type="text" id="code" name="code" required placeholder="Code de réinitialisation">
            </div>
            <button type="submit" name="verify_code">Vérifier le code</button>
        </form>
    </div>

    <!-- Étape 3: Changement du mot de passe -->
    <div class="step <?= ($step == 3) ? 'active' : ''; ?>">
        <h2>Changer votre mot de passe</h2>
        <form method="POST">
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" id="new_password" name="new_password" required placeholder="Nouveau mot de passe">
            </div>
            <button type="submit" name="change_password">Changer le mot de passe</button>
        </form>
    </div>
</div>

</body>
</html>
