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

// Vérification si le formulaire a été soumis
if (isset($_POST['subject'], $_POST['course_title'], $_POST['exercise_title']) && isset($_FILES['file'], $_FILES['exercise_file'])) {
    $subject = $_POST['subject'];
    $courseTitle = $_POST['course_title'];
    $exerciseTitle = $_POST['exercise_title'];

    // Vérification de l'upload des fichiers
    $course_file = $_FILES['file']; // Fichier de cours
    $exercise_file = $_FILES['exercise_file']; // Fichier d'exercice

    // Vérifier les erreurs d'upload
    if ($course_file['error'] !== UPLOAD_ERR_OK) {
        die("Erreur lors de l'upload du fichier de cours.");
    }
    if ($exercise_file['error'] !== UPLOAD_ERR_OK) {
        die("Erreur lors de l'upload du fichier d'exercice.");
    }

    // Définir les chemins de destination pour les fichiers téléchargés
    $upload_dir = 'uploads/'; // Dossier où les fichiers seront enregistrés
    $course_file_path = $upload_dir . basename($course_file['name']);
    $exercise_file_path = $upload_dir . basename($exercise_file['name']);

    // Déplacer les fichiers vers le dossier de destination
    if (!move_uploaded_file($course_file['tmp_name'], $course_file_path)) {
        die("Erreur lors du déplacement du fichier de cours.");
    }
    if (!move_uploaded_file($exercise_file['tmp_name'], $exercise_file_path)) {
        die("Erreur lors du déplacement du fichier d'exercice.");
    }

    // Insérer les informations dans la base de données
    $sql = "INSERT INTO uploads (subject, course_title, exercise_title, course_file, exercise_file) 
            VALUES (:subject, :course_title, :exercise_title, :course_file, :exercise_file)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':course_title', $courseTitle); // Correction de la variable $course_title
    $stmt->bindParam(':exercise_title', $exerciseTitle); // Correction de la variable $exercise_title
    $stmt->bindParam(':course_file', $course_file_path);
    $stmt->bindParam(':exercise_file', $exercise_file_path);

    if ($stmt->execute()) {
        echo "Les fichiers ont été téléchargés avec succès.";
    } else {
        echo "Erreur lors de l'insertion des informations dans la base de données.";
    }
} else {
    echo "Aucune donnée n'a été envoyée.";
}
?>
