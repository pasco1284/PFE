<?php
// Connexion à la base de données
$host = '57.129.134.101';
$dbname = 'siteweb';
$username = 'root';
$password = '12345678';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

session_start();
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour accéder à cette page.");
}
$user_id = $_SESSION['user_id'];

$sql = "SELECT id, firstname, lastname, email, role, elements, created_at, photo FROM accounts WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur non trouvé.");
}

// Vérification de la photo
$photo = ($user['photo'] && file_exists('images/' . $user['photo'])) ? $user['photo'] : 'default-profile.png';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Profil</title>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
    display: flex;
    align-items: center; /* Centre verticalement */
    justify-content: center; /* Centre horizontalement */
    height: 100vh; /* Utilise toute la hauteur de la fenêtre */
    margin: 0;
    overflow: hidden;
}

#particles-js {
    height: 100%;
}

.profile-container {
    position: absolute;
    background-color: (0,0,0,.9);
    border-radius: 10px;
    box-shadow: 0 15px 25px #00000098;
    width: 90%;
    max-width: 900px;
    padding: 30px;
    margin-top: 30px;
    transition: transform 0.3s ease-in-out;
}

.profile-container:hover {
    transform: translateY(-10px);
}

h1 {
    text-align: center;
    color: white;
    margin-bottom: 20px;
    font-size: 2rem;
}

.profile-photo {
    text-align: center;
    margin-bottom: 30px;
}

.profile-photo img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid #2575FC;
}

.profile-details {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}

.profile-details div {
    width: 48%;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: white;
}

p {
    color: #777;
    font-size: 1rem;
    line-height: 1.6;
}

.btn {
    display: inline-block;
    background-color: #2575FC;
    color: #fff;
    padding: 12px 20px;
    text-decoration: none;
    font-size: 1rem;
    border-radius: 5px;
    text-align: center;
    transition: background-color 0.3s ease;
    margin-top: 20px;
}

.btn:hover {
    background-color: #6A11CB;
}

@media (max-width: 768px) {
    .profile-details {
        flex-direction: column;
    }

    .profile-details div {
        width: 100%;
    }

    h1 {
        font-size: 1.5rem;
    }
}

    </style>
</head>
<body>
<div id="particles-js"></div>
    <script type="text/javascript" src="images/particles.js"></script>
    <script type="text/javascript" src="images/app-login.js"></script>

    <div class="profile-container">
        <h1>Profil de l'utilisateur</h1>

        <div class="profile-photo">
            <img src="images/<?php echo htmlspecialchars($photo); ?>" alt="Photo de profil">
        </div>

        <div class="profile-details">
            <div>
                <label>Prénom:</label>
                <p><?php echo htmlspecialchars($user['firstname']); ?></p>

                <label>Nom:</label>
                <p><?php echo htmlspecialchars($user['lastname']); ?></p>

                <label>Email:</label>
                <p><?php echo htmlspecialchars($user['email']); ?></p>

                <label>Rôle:</label>
                <p><?php echo htmlspecialchars($user['role']); ?></p>
            </div>

            <div>
                <label>Éléments sélectionnés:</label>
                <p><?php echo htmlspecialchars($user['elements']); ?></p>

                <label>Date d'inscription:</label>
                <p><?php echo htmlspecialchars($user['created_at']); ?></p>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="http://57.129.134.101/edit-profile.php" class="btn">Modifier le Profil</a>
        </div>
    </div>

</body>
</html>
