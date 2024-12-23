<?php
// Connexion à la base de données
$host = 'localhost'; // Votre hôte
$dbname = 'siteweb'; // Nom de la base de données
$username = 'root'; // Utilisateur de la base de données
$password = ''; // Mot de passe de la base de données

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

// Requête pour récupérer les fichiers depuis la base de données
$sql = "SELECT id, subject, course_title, exercise_title, course_file, exercise_file FROM uploads";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Récupérer tous les fichiers
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Liste des Cours et Exercices</title>
</head>
<body>

<!-- Profile Menu -->
<div class="profile-menu">
    <img src="images/<?php echo htmlspecialchars($photo); ?>" alt="Votre photo de profil" class="profile-icon" id="profileIcon" onclick="toggleMenu()">
  
    <i class="fas fa-comments chat-icon" id="messengerIcon" onclick="openMessenger()"></i>
  
    <div class="dropdown-menu" id="dropdownMenu" style="display: none;">
        <ul>
            <li><a href="http://localhost/siteweb/Profile.php">Accéder au profil</a></li>
            <li><a href="http://localhost:4321/home">Se déconnecter</a></li>
        </ul>
    </div>
</div>

<script>
         // Attendre que la fenêtre soit entièrement chargée
 window.addEventListener("load", function() {
    // Fonction pour afficher ou masquer le menu déroulant
    function toggleMenu() {
        const dropdownMenu = document.getElementById("dropdownMenu");
        dropdownMenu.style.display = dropdownMenu.style.display === "none" ? "block" : "none";
    }

    // Fonction pour rediriger vers la page de messagerie
    function openMessenger() {
        window.location.href = "http://localhost/siteweb/Messanger.php"; // Remplacez "messagerie.html" par le chemin de votre page de messagerie
    }    
    // Ajouter des événements sur les éléments pour éviter l'utilisation de `onclick` directement dans le HTML
    document.getElementById("profileIcon").addEventListener("click", toggleMenu);
    document.getElementById("messengerIcon").addEventListener("click", openMessenger);
});
    </script>

    <div id="particles-js"></div>
    <script type="text/javascript" src="images/particles.js"></script>
    <script type="text/javascript" src="images/app-login.js"></script>

    <div class="container-file">
        <h2>Liste des Cours et Exercices : </h2>
        <table>
            <thead>
                <tr>
                    <th>Sujet:ㅤ</th>
                    <th>Titre du Cours:ㅤ</th>
                    <th>Titre de l'Exercice:ㅤ</th>
                    <th>Télécharger le Cours:ㅤ</th>
                    <th>Télécharger l'Exercice:ㅤ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($files):
                    foreach ($files as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['subject']); ?></td>
                            <td><?php echo htmlspecialchars($file['course_title']); ?></td>
                            <td><?php echo htmlspecialchars($file['exercise_title']); ?></td>
                            <td><a href="/uploads/<?php echo htmlspecialchars($file['course_file']); ?>" class="download-btn" download>Télécharger</a></td>
                            <td><a href="/uploads/<?php echo htmlspecialchars($file['exercise_file']); ?>" class="download-btn" download>Télécharger</a></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr><td colspan="5">Aucun fichier trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <style>
 * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    overflow-x: hidden; 
    height: 100vh;
    background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
    background-size: cover;
      background-attachment: fixed;
      background-repeat: no-repeat; 
      background-position: center;
      margin: 0; 
      padding: 0; 
      align-items: flex-end;
      justify-content: flex-end;
  }
  
  #particles-js {
    height: 100%;
  }
  
  .container-file {
    max-width: 800px; 
    margin: 20px; 
    padding: 30px; 
    background-color: rgba(0, 0, 0, 0.253); 
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
    text-align: left; 
   position: absolute;
   top: 5%;
  }

  th{
    color: white;
  }
  h2{
    color: blueviolet;
  }
  td{
    color: red;
  }
  
  .download-section h2 {
    text-align: left; 
    font-size: 24px;
    color: #ffffff;
    margin-bottom: 20px;
  }
  
  .course-category {
    margin-bottom: 40px;
    padding: 20px;
    background-color: transparent;
    border-left: 5px solid #007BFF;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
  }
  
  .course-category h3 {
    font-size: 20px;
    color: #007BFF;
    margin-bottom: 10px;
    text-align: left; 
  }
  
  
  .file-list {
    list-style-type: none;
    padding: 0px;
    margin: 20px;
  }
  
  .file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
  }
  
  .file-item:last-child {
    border-bottom: none;
  }
  
  .file-item span {
    font-size: 16px;
    color: #ffffff;
    text-align: left; 
  }
  
  
  .file-item a {
    color: #007BFF;
    text-decoration: none;
    font-weight: bold;
  }
  
  .file-item a:hover {
    text-decoration: underline;
  }
  
  
  .file-item a:hover {
    text-decoration: underline;
  }
  
  
  .profile-menu {
    position: absolute;
    top: 30px;
    right: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  
  .profile-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid white;
  }
  
  
  .dropdown-menu {
    position: absolute;
    top: 60px;
    right: 0;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    overflow: hidden;
  }
  
  .dropdown-menu ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  .dropdown-menu ul li {
    padding: 10px;
    text-align: left;
  }
  
  .dropdown-menu ul li a {
    text-decoration: none;
    color: #000;
    display: block;
  }
  
  .dropdown-menu ul li:hover {
    background-color: #f0f0f0;
  }
  .chat-icon {
  font-size: 24px;
  color: #ffffff;
  margin-left: 15px;
  cursor: pointer;
  position: absolute;
  top: 10%;
  right: 200%;
  }

  @media (max-width: 768px) {
    /* Adjust container width and padding for tablets */
    .container-file {
      max-width: 600px;
      padding: 20px;
    }
  
    /* Adjust the profile menu and icons for smaller screens */
    .profile-menu {
      flex-direction: row;
      top: 15px;
      right: 15px;
    }

    .dropdown-menu {
      position: absolute;
      top: 40px;
      right: 0;
      background-color: white;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      border-radius: 5px;
      overflow: hidden;
    }
  
    .profile-icon {
      width: 40px;
      height: 40px;
    }
  
    .chat-icon {
      font-size: 20px;
      position: absolute;
      top: 10%;
      right: 200%;
      }
    }

  
  @media (max-width: 550px) {
    /* Smaller container for mobile screens */
    .container-file {
      max-width: 90%;
      padding: 15px;
      top: 30%;
    }
  
    /* Profile and chat icons resize */
    .profile-icon {
      width: 35px;
      height: 35px;
    }
  
    .chat-icon {
      font-size: 18px;
      position: absolute;
      top: 10%;
      right: 1200%;
      }
      
    }
  
    /* Resize text in file items */
    .file-item span {
      font-size: 14px;
    }
    
    .file-item a {
      font-size: 14px;
    }
  
    </style>

</body>
</html>
