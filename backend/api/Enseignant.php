<?php
// Connexion à la base de données
$host = 'localhost';
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

// Récupérer les infos de l'utilisateur
$sql = "SELECT id, firstname, lastname FROM accounts WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    die("Utilisateur non trouvé.");
}

// Dossier d'upload et fichier de logs
$upload_dir = 'uploads/';
$log_file = 'logs.txt';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$forbidden_extensions = ['exe', 'sh', 'bat', 'cmd', 'php', 'js'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['subject'], $_POST['course_title'], $_POST['exercise_title'], $_POST['level']) &&
        isset($_FILES['file'], $_FILES['exercise_file'])) {

        $subject = $_POST['subject'];
        $courseTitle = $_POST['course_title'];
        $exerciseTitle = $_POST['exercise_title'];
        $level = $_POST['level'];

        $course_file = $_FILES['file'];
        $exercise_file = $_FILES['exercise_file'];

        $course_ext = pathinfo($course_file['name'], PATHINFO_EXTENSION);
        $exercise_ext = pathinfo($exercise_file['name'], PATHINFO_EXTENSION);

        if (in_array($course_ext, $forbidden_extensions) || in_array($exercise_ext, $forbidden_extensions)) {
            $message = "Fichier interdit détecté ! Veuillez envoyer un format autorisé.";
        } elseif ($course_file['error'] === UPLOAD_ERR_OK && $exercise_file['error'] === UPLOAD_ERR_OK) {
            $course_file_path = $upload_dir . basename($course_file['name']);
            $exercise_file_path = $upload_dir . basename($exercise_file['name']);

            if (move_uploaded_file($course_file['tmp_name'], $course_file_path) &&
                move_uploaded_file($exercise_file['tmp_name'], $exercise_file_path)) {

                // ✅ Insertion avec user_id
                $sql = "INSERT INTO uploads (user_id, subject, course_title, exercise_title, level, course_file, exercise_file)
                        VALUES (:user_id, :subject, :course_title, :exercise_title, :level, :course_file, :exercise_file)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':subject', $subject);
                $stmt->bindParam(':course_title', $courseTitle);
                $stmt->bindParam(':exercise_title', $exerciseTitle);
                $stmt->bindParam(':level', $level);
                $stmt->bindParam(':course_file', $course_file_path);
                $stmt->bindParam(':exercise_file', $exercise_file_path);

                if ($stmt->execute()) {
                    $message = "Les fichiers ont été téléchargés avec succès.";

                    // ✅ Log avec ID utilisateur
                    $log_entry = "[" . date("Y-m-d H:i:s") . "] ID: {$user['id']} | {$user['firstname']} {$user['lastname']} | Cours: $courseTitle | Exercice: $exerciseTitle | Fichiers: " . basename($course_file['name']) . ", " . basename($exercise_file['name']) . "\n";
                    file_put_contents($log_file, $log_entry, FILE_APPEND);
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
  <title>Enseignant - Upload</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: radial-gradient(ellipse at bottom, #0b358f 0%, #000000 100%);
      color: #fff;
      padding: 0;
      margin: 0;
    }
    .container-upload {
      width: 90%;
      max-width: 800px;
      margin: 60px auto;
      background: rgba(0, 0, 9, 0.42);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      text-transform: uppercase;
    }
    form label {
      display: block;
      margin: 15px 0 5px;
    }
    input[type="text"], input[type="file"], select {
      width: 100%;
      padding: 12px;
      border: 2px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      color: #000;
      background: #f9f9f9;
    }
    input[type="file"]::file-selector-button {
      background-color: #27096b;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    input[type="file"]::file-selector-button:hover {
      background-color: #1b054b;
    }
    button {
      margin-top: 20px;
      width: 100%;
      padding: 12px;
      font-size: 1rem;
      background-color: #27096b;
      border: none;
      border-radius: 8px;
      color: white;
      cursor: pointer;
    }
    button:hover {
      background-color: #1b054b;
      transform: scale(1.05);
    }
    p {
      text-align: center;
      font-weight: bold;
      color: #ffcc00;
    }
  </style>
</head>
<body>
  <div class="container-upload">
    <h2>Upload de Cours</h2>
    <?php if ($message): ?>
      <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <label for="subject">Matière</label>
      <input type="text" id="subject" name="subject" required>

      <label for="course_title">Titre du Cours</label>
      <input type="text" id="course_title" name="course_title" required>

      <label for="exercise_title">Titre de l'Exercice</label>
      <input type="text" id="exercise_title" name="exercise_title" required>

      <label for="level">Élément</label>
      <select id="level" name="level" required>
        <option value="L1">L1</option>
        <option value="L2">L2</option>
        <option value="L3">L3</option>
        <option value="M1">M1</option>
        <option value="M2">M2</option>
      </select>

      <label for="file">Fichier de cours</label>
      <input type="file" id="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required>

      <label for="exercise_file">Fichier d'exercice</label>
      <input type="file" id="exercise_file" name="exercise_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required>

      <button type="submit">Télécharger</button>
    </form>
  </div>
</body>
</html>
