<?php
// Connexion à la base de données
$host = 'localhost'; // Votre hôte
$dbname = 'siteweb'; // Nom de la base de données
$username = 'root'; // Utilisateur de la base de données
$password = '12345678'; // Mot de passe de la base de données

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

// Message de confirmation ou d'erreur
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['subject'], $_POST['course_title'], $_POST['exercise_title']) && isset($_FILES['file'], $_FILES['exercise_file'])) {
        $subject = $_POST['subject'];
        $courseTitle = $_POST['course_title'];
        $exerciseTitle = $_POST['exercise_title'];

        // Fichiers uploadés
        $course_file = $_FILES['file'];
        $exercise_file = $_FILES['exercise_file'];

        // Vérification des erreurs d'upload
        if ($course_file['error'] === UPLOAD_ERR_OK && $exercise_file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            $course_file_path = $upload_dir . basename($course_file['name']);
            $exercise_file_path = $upload_dir . basename($exercise_file['name']);

            // Déplacement des fichiers
            if (move_uploaded_file($course_file['tmp_name'], $course_file_path) && move_uploaded_file($exercise_file['tmp_name'], $exercise_file_path)) {
                // Insertion dans la base de données
                $sql = "INSERT INTO uploads (subject, course_title, exercise_title, course_file, exercise_file) 
                        VALUES (:subject, :course_title, :exercise_title, :course_file, :exercise_file)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':subject', $subject);
                $stmt->bindParam(':course_title', $courseTitle);
                $stmt->bindParam(':exercise_title', $exerciseTitle);
                $stmt->bindParam(':course_file', $course_file_path);
                $stmt->bindParam(':exercise_file', $exercise_file_path);

                if ($stmt->execute()) {
                    $message = "Les fichiers ont été téléchargés avec succès.";
                } else {
                    $message = "Erreur lors de l'insertion dans la base de données.";
                }
            } else {
                $message = "Erreur lors du déplacement des fichiers.";
            }
        } else {
            $message = "Erreur lors de l'upload des fichiers.";
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Cours par Enseignants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="/icon.png">
</head>
<body>
    <div id="particles-js"></div>
    <script type="text/javascript" src="images/particles.js"></script>
    <script type="text/javascript" src="images/app-login.js"></script>

    <div class="profile-menu">
    <img src="images/<?php echo htmlspecialchars($photo); ?>" alt="Votre photo de profil" class="profile-icon" id="profileIcon" onclick="toggleMenu()">
  
    <i class="fas fa-comments chat-icon" id="messengerIcon" onclick="openMessenger()"></i>
  
    <div class="dropdown-menu" id="dropdownMenu" style="display: none;">
        <ul>
            <li><a href="http://57.129.134.101/Profile.php">Accéder au profil</a></li>
            <li><a href="http://57.129.134.101/home">Se déconnecter</a></li>
        </ul>
    </div>
</div>
    
    <div class="container-upload">
        <h2>Upload de Cours</h2>
        <?php if ($message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form id="upload-form" method="post" enctype="multipart/form-data">
            <label for="subject">Matière</label>
            <input type="text" id="subject" name="subject" required>
            
            <label for="course_title">Titre du Cours</label>
            <input type="text" id="course_title" name="course_title" required>
            
            <label for="exercise_title">Titre de l'Exercice</label>
            <input type="text" id="exercise_title" name="exercise_title" required>

            <div class="container">
  <div class="folder">
    <div class="front-side">
      <div class="tip"></div>
      <div class="cover"></div>
    </div>
    <div class="back-side cover"></div>
  </div>
</div>

            
            <label for="file">Fichier de cours</label>
            <input type="file" id="file" name="file" required>

            <div class="container">
  <div class="folder">
    <div class="front-side">
      <div class="tip"></div>
      <div class="cover"></div>
    </div>
    <div class="back-side cover"></div>
  </div>
</div>

            
            <label for="exercise_file">Fichier d'exercice</label>
            <input type="file" id="exercise_file" name="exercise_file" required>
            
            <button type="submit">Télécharger</button>
        </form>
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
    height: 100%;
    background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
  }

  button {
    margin: 10px;
  }
  
  #particles-js {
    height: 150%;
  }
  .container-upload {
    position: absolute;
    top: 8%;
    left: 5%;
    width: 90%;
    max-width: 800px;
    padding: 30px;
    background: rgba(0, 0, 9, 0.42);
    border-radius: 12px;
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
}
    
h2 {
    text-align: center;
    font-size: 1.8rem;
    color: #ffffff;
    margin-bottom: 20px;
    text-transform: uppercase;
}
    
    .upload-section {
      margin-bottom: 15px;
      color: white;
    }
    
    label {
    font-size: 1rem;
    color: white;
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
}
    
input[type="text"],
input[type="file"] {
    width: 100%;
    padding: 12px;
    border: 2px solid #ccc;
    border-radius: 8px;
    background-color: #f9f9f9;
    font-size: 1rem;
    transition: all 0.3s ease;
    color: #333;
}

input[type="text"]:focus,
input[type="file"]:focus {
    border-color: #27096b;
    box-shadow: 0 0 8px rgba(39, 9, 107, 0.5);
    outline: none;
}

input[type="file"]::file-selector-button {
    padding: 10px 20px;
    background-color: #27096b;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input[type="file"]::file-selector-button:hover {
    background-color: #1b054b;
}
    
button {
    padding: 12px 30px;
    background-color: #27096b;
    color: white;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

button:hover {
    background-color: #1b054b;
    transform: scale(1.05);
}

    /* From Uiverse.io by 3bdel3ziz-T */ 
.container {
  --transition: 350ms;
  --folder-W: 120px;
  --folder-H: 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  padding: 10px;
  background: transparent;
  border-radius: 15px;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
  height: calc(var(--folder-H) * 1.7);
  position: relative;
}

.folder {
  position: absolute;
  top: 30px;
  left: calc(50% - 60px);
  animation: float 2.5s infinite ease-in-out;
  transition: transform var(--transition) ease;
}

.folder:hover {
  transform: scale(1.05);
}

.folder .front-side,
.folder .back-side {
  position: absolute;
  transition: transform var(--transition);
  transform-origin: bottom center;
}

.folder .back-side::before,
.folder .back-side::after {
  content: "";
  display: block;
  background-color: white;
  opacity: 0.5;
  z-index: 0;
  width: var(--folder-W);
  height: var(--folder-H);
  position: absolute;
  transform-origin: bottom center;
  border-radius: 15px;
  transition: transform 350ms;
  z-index: 0;
}

.container:hover .back-side::before {
  transform: rotateX(-5deg) skewX(5deg);
}
.container:hover .back-side::after {
  transform: rotateX(-15deg) skewX(12deg);
}

.folder .front-side {
  z-index: 1;
}

.container:hover .front-side {
  transform: rotateX(-40deg) skewX(15deg);
}

.folder .tip {
  background: linear-gradient(135deg, #ff9a56, #ff6f56);
  width: 80px;
  height: 20px;
  border-radius: 12px 12px 0 0;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  position: absolute;
  top: -10px;
  z-index: 2;
}

.folder .cover {
  background: linear-gradient(135deg, #ffe563, #ffc663);
  width: var(--folder-W);
  height: var(--folder-H);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
  border-radius: 10px;
}
@keyframes float {
  0% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(-20px);
  }

  100% {
    transform: translateY(0px);
  }
}

    
    .profile-menu {
      position: absolute;
      top: 20px;
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
      top: 40px;
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
    top: 35%;
    right: 200%;
  }
    </style>
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
        window.location.href = "http://57.129.134.101/Messanger.php"; // Remplacez "messagerie.html" par le chemin de votre page de messagerie
    }    
    // Ajouter des événements sur les éléments pour éviter l'utilisation de `onclick` directement dans le HTML
    document.getElementById("profileIcon").addEventListener("click", toggleMenu);
    document.getElementById("messengerIcon").addEventListener("click", openMessenger);
});
    </script>

</body>
</html>
