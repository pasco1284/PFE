<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$image = $_FILES['image'];

if ($image['error'] != 0) {
    echo json_encode(['status' => 'error', 'message' => 'File upload error']);
    exit();
}

// Spécifier le répertoire cible pour l'upload
$target_dir = "../images/";  // Vous pouvez ajuster le chemin en fonction de la structure de votre projet
$target_file = $target_dir . basename($image["name"]);

// Vérifiez si le fichier est bien une image
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($imageFileType, $allowed_types)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type']);
    exit();
}

// Déplacez le fichier vers le répertoire cible
if (move_uploaded_file($image["tmp_name"], $target_file)) {
    // Connexion à la base de données
    $conn = new mysqli('localhost', 'root', '12345678', 'siteweb');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insérer le message dans la base de données
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type) VALUES (?, ?, ?, 'image')");
    $image_url = '/images/' . basename($image["name"]); // URL relative de l'image
    $stmt->bind_param("iis", $user_id, $receiver_id, $image_url);
    $stmt->execute();
    $stmt->close();

    // Réponse au format JSON
    echo json_encode(['status' => 'success', 'image_url' => $image_url]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
}
?>
