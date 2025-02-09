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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = $_POST['code'];

    // Vérifier si le code est correct
    $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = ? AND reset_code = ?");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Code valide. Vous pouvez changer votre mot de passe.";
    } else {
        echo "Code incorrect.";
    }
}
?>
<?php
// Vous pouvez ajouter une logique PHP ici pour vérifier si le code est valide.
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/icon.png">
    <title>Vérification du Code</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            height: 100vh;
            background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #222;
            color: #fff;
            outline: none;
        }

        .form-group input:focus {
            border-color: #007bff;
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .form-group button:hover {
            background: #0056b3;
        }

        .back-link {
            margin-top: 15px;
            font-size: 14px;
            color: #ddd;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div id="particles-js"></div>
    <script type="text/javascript" src="images/particles.js"></script>
    <script type="text/javascript" src="images/app-login.js"></script>

    <div class="form-container">
        <h2>Vérification du Code de Réinitialisation</h2>
        <form method="POST" action="verify_code.php">
            <div class="form-group">
                <label for="code" style="color: #fff;">Entrez le code reçu par email</label>
                <input type="text" id="code" name="code" placeholder="Code de réinitialisation" required>
            </div>
            <div class="form-group">
                <button type="submit">Vérifier le code</button>
            </div>
            <div class="back-link">
                <p>Retour à l'<a href="http://57.129.134.101/paassword">étape précédente</a></p>
            </div>
        </form>
    </div>
</body>
</html>

