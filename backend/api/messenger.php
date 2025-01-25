<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'chat_db'; // Nom de votre base de données
$username = 'root'; // Votre nom d'utilisateur MySQL
$password = ''; // Votre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Échec de la connexion : " . $e->getMessage();
}

// Fonction pour récupérer les messages entre deux utilisateurs
function getMessages($sender_id, $receiver_id) {
    global $pdo;
    $sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $messages;
}

// Envoi d'un message texte
if (isset($_POST['message'])) {;
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sender_id, $receiver_id, $message]);

    echo json_encode(['status' => 'success']);
    exit;
}

// Envoi d'un fichier audio
if (isset($_FILES['audio'])) {
    $sender_id = $_POST['sender_id'];
    $receiver_id = $_POST['receiver_id'];
    $audio = $_FILES['audio'];

    $upload_dir = 'uploads/audio/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $audio_name = basename($audio['name']);
    $audio_path = $upload_dir . $audio_name;

    if (move_uploaded_file($audio['tmp_name'], $audio_path)) {
        // Insérer le message audio dans la base de données
        $sql = "INSERT INTO messages (sender_id, receiver_id, file_url, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sender_id, $receiver_id, $audio_path]);

        echo json_encode(['status' => 'success', 'file_url' => $audio_path]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Échec de l\'upload de l\'audio.']);
    }
    exit;
}

// Récupération des messages
if (isset($_GET['sender_id']) && isset($_GET['receiver_id'])) {
    $sender_id = $_GET['sender_id'];
    $receiver_id = $_GET['receiver_id'];
    $messages = getMessages($sender_id, $receiver_id);

    // Retourner les messages sous forme JSON
    echo json_encode($messages);
    exit;
}
?>