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

// Récupérer l'élément de l'étudiant
$student_element = $user['elements']; // Cela suppose que "elements" contient l'élément choisi par l'étudiant

// Requête pour récupérer les fichiers filtrés selon l'élément de l'étudiant
$sql = "SELECT id, subject, course_title, exercise_title, course_file, exercise_file FROM uploads WHERE subject = :subject";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':subject', $student_element, PDO::PARAM_STR);
$stmt->execute();

// Récupérer tous les fichiers filtrés
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
            <li><a href="http://57.129.134.101/Profile.php">Accéder au profil</a></li>
            <li><a href="http://57.129.134.101/home">Se déconnecter</a></li>
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
        window.location.href = "http://57.129.134.101/Messanger.php"; // Remplacez "messagerie.html" par le chemin de votre page de messagerie
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
                    <tr><td colspan="5">Aucun fichier trouvé pour cet élément.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
