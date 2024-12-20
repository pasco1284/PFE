<?php
// Inclure la connexion à la base de données
include_once '_Database.php';

// Vérifier si l'utilisateur est connecté (par exemple, via la session)
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Rediriger si l'utilisateur n'est pas connecté
    exit;
}

$email = $_SESSION['email']; // Récupérer l'email de l'utilisateur connecté

// Requête pour obtenir les données de l'utilisateur
$sql = "SELECT * FROM accounts WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email); // l'email est passé en paramètre
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
    $role = $user['role'];
    $niveau = $user['elements']; // Si vous avez un champ 'niveau' dans la table
    $photo = $user['photo'];
} else {
    echo "Utilisateur introuvable.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/icon.png">
    <title>Profil</title>
</head>
<body>
    <div id="particles-js"></div>
    <script src="/particles.js" defer></script>
    <script src="/app-login.js" defer></script>
    
    <div class="profile-menu">
        <img src="images/photo_profile.jpg" alt="Votre photo de profil" class="profile-icon" id="profileIcon" onclick="toggleMenu()">
        <div class="dropdown-menu" id="dropdownMenu" style="display: none;">
            <ul>
                <li><a href="Profile">Accéder à votre interface</a></li>
                <li><a href="http://localhost:4321/home">Se déconnecter</a></li>
            </ul>
        </div>
    </div>

    <div class="profile-container">
        <form class="profile-form" method="POST" enctype="multipart/form-data">
            <div class="profile-header">
                <img src="images/<?php echo $photo; ?>" alt="Photo de profil" class="profile-pic" id="profilePic">
                <input type="file" id="profilePicInput" name="profilePicInput" style="display:none;">
                <label for="profilePicInput" class="upload-button">Changer la photo de profil</label>
                <h2 id="fullname"><?php echo $firstname . ' ' . $lastname; ?></h2>
            </div>

            <div class="form-section">
                <label for="nom"><strong>Nom:</strong></label>
                <input type="text" id="nom" name="nom" value="<?php echo $lastname; ?>" readonly>
            </div>

            <div class="form-section">
                <label for="prenom"><strong>Prénom:</strong></label>
                <input type="text" id="prenom" name="prenom" value="<?php echo $firstname; ?>" readonly>
            </div>

            <div class="form-section">
                <label for="role"><strong>Rôle:</strong></label>
                <input type="text" id="role" name="role" value="<?php echo $role; ?>" readonly>
            </div>

            <div class="form-section">
                <label for="niveau"><strong>Niveau:</strong></label>
                <input type="text" id="niveau" name="niveau" value="<?php echo $niveau; ?>" readonly>
            </div>

            <button type="button" id="editButton">Modifier le Profil</button>
            <button type="submit" id="saveButton" style="display:none;">Enregistrer</button>
        </form>
    </div>
    <style>
        html {
    font-size: 100%; 
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    height: 100vh;
    background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
}

#particles-js {
    height: 100%;
}

/* Menu du profil */
.profile-menu {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1;
        }
        
        .profile-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid white;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .profile-icon:hover {
            transform: scale(1.1);
        }
        
        /* Menu déroulant */
        .dropdown-menu {
            position: absolute;
            top: 60px;
            right: 0;
            background-color: white;
            color: #000;
            border-radius: 5px;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .dropdown-menu ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .dropdown-menu ul li {
            padding: 10px;
        }
        
        .dropdown-menu ul li a {
            text-decoration: none;
            color: #000;
        }
        
        .dropdown-menu ul li:hover {
            background-color: #f0f0f0;
        }
        
        /* Container du profil */
        .profile-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 800px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        /* En-tête du profil */
        .profile-header {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            margin-bottom: 15px;
        }
        
        .upload-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0b358f;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .upload-button:hover {
            background-color: #082d75;
        }
        
        h2#fullname {
            font-size: 24px;
            margin-top: 10px;
        }
        
        /* Sections du formulaire */
        .form-section {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        
        label {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        input {
            padding: 12px;
            font-size: 16px;
            background-color: #f2f2f2;
            border: none;
            border-radius: 5px;
            color: #333;
            transition: background-color 0.3s ease;
        }
        
        input:read-only {
            background-color: #e0e0e0;
        }
        
        button {
            padding: 10px 20px;
            background-color: #0b358f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
        }
        
        button:hover {
            background-color: #082d75;
        }
        
        button:disabled {
            background-color: #999;
        }
        
        /* Media Queries pour les écrans mobiles */
        @media (max-width: 768px) {
            .profile-container {
                width: 90%;
                padding: 30px;
            }
        
            .profile-pic {
                width: 100px;
                height: 100px;
            }
        
            h2#fullname {
                font-size: 20px;
            }
        
            .form-section {
                flex-direction: column;
                gap: 10px;
            }
        }
        
        @media (max-width: 550px) {
            .profile-container {
                width: 95%;
                padding: 20px;
            }
        
            .profile-pic {
                width: 80px;
                height: 80px;
            }
        
            h2#fullname {
                font-size: 18px;
            }
        
            button {
                padding: 8px 16px;
                font-size: 14px;
            }
        
            .upload-button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>

    <?php
    // Traitement de la mise à jour de la photo de profil
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profilePicInput'])) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["profilePicInput"]["name"]);
        
        // Vérifier si le fichier est une image
        if (getimagesize($_FILES["profilePicInput"]["tmp_name"]) !== false) {
            if (move_uploaded_file($_FILES["profilePicInput"]["tmp_name"], $target_file)) {
                // Mettre à jour la photo dans la base de données
                $sql = "UPDATE accounts SET photo = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $target_file, $email);
                $stmt->execute();

                // Rafraîchir la page pour voir la nouvelle photo
                header("Location: profile.php");
            } else {
                echo "Erreur de téléchargement de la photo.";
            }
        } else {
            echo "Le fichier n'est pas une image.";
        }
    }
    ?>
</body>
</html>
