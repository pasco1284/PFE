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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Traitement des données du formulaire
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $elements = $_POST['elements'];

    // Traitement de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Vérifier si le fichier est une image
        $imageType = mime_content_type($_FILES['photo']['tmp_name']);
        if (strpos($imageType, 'image') === false) {
            die("Le fichier téléchargé n'est pas une image.");
        }

        // Définir le nom du fichier
        $photoName = time() . '_' . $_FILES['photo']['name'];
        $photoPath = 'images/' . $photoName;

        // Déplacer le fichier téléchargé vers le dossier images
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
            die("Erreur lors du téléchargement de la photo.");
        }

        // Mettre à jour le nom de la photo dans la base de données
        $update_photo_sql = "UPDATE accounts SET photo = :photo WHERE id = :user_id";
        $update_photo_stmt = $pdo->prepare($update_photo_sql);
        $update_photo_stmt->bindParam(':photo', $photoName);
        $update_photo_stmt->bindParam(':user_id', $user_id);
        $update_photo_stmt->execute();
    }

    // Mise à jour des autres données
    $update_sql = "UPDATE accounts SET firstname = :firstname, lastname = :lastname, email = :email, elements = :elements WHERE id = :user_id";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':firstname', $firstname);
    $update_stmt->bindParam(':lastname', $lastname);
    $update_stmt->bindParam(':email', $email);
    $update_stmt->bindParam(':elements', $elements);
    $update_stmt->bindParam(':user_id', $user_id);
    $update_stmt->execute();

    // Rediriger après la mise à jour
    header("Location: http://57.129.134.101/Profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Profil</title>
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
            align-items: center;
            justify-content: center;
            height: 100vh;
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

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: white;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
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

    </style>
</head>
<body>

<div id="particles-js"></div>
    <script type="text/javascript" src="images/particles.js"></script>
    <script type="text/javascript" src="images/app-login.js"></script>

    <div class="profile-container">
    <h1>Modifier Profil</h1>

    <form method="POST" action="edit-profile.php" enctype="multipart/form-data">


    <div class="profile-photo">
            <img src="images/<?php echo $photo; ?>" alt="Photo de Profil">
            <label for="photo">Modifier la photo de profil:</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div>
            <label for="firstname">Prénom:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
        </div>

        <div>
            <label for="lastname">Nom:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
        </div>

        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div>
            <label for="elements">Éléments sélectionnés:</label>
            <input type="text" id="elements" name="elements" value="<?php echo htmlspecialchars($user['elements']); ?>" disabled>
        </div>

        <div>
            <label for="role">Rôle:</label>
            <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" disabled>
        </div>

        <div>
    <button type="submit" class="btn">Mettre à jour le profil</button>
        </div>
    </form>
</div>

</body>
</html>